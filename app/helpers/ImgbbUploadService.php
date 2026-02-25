<?php
/*
 * Copyright (c) 2025 AltumCode (https://altumcode.com/)
 *
 * This software is licensed exclusively by AltumCode and is sold only via https://altumcode.com/.
 * Unauthorized distribution, modification, or use of this software without a valid license is not permitted and may be subject to applicable legal actions.
 */

namespace Altum\Helpers;

use Altum\Models\ImgbbUploads;

defined('ALTUMCODE') || die();

class ImgbbUploadService {

    /* Single fallback value (MB) used only if no relevant setting exists */
    const FALLBACK_MAX_SIZE_MB = 3;

    const ALLOWED_MIMES = ['image/png', 'image/jpeg', 'image/gif', 'image/webp'];

    protected $user;
    protected ImgbbUploads $imgbb_uploads_model;

    public function __construct($user) {
        $this->user = $user;
        $this->imgbb_uploads_model = new ImgbbUploads();
    }

    /**
     * Sync-only upload to ImgBB. No local persistence.
     *
     * @return array{status:int,data:array}
     */
    public function upload(array $file_post, ?string $entity_type = null, ?int $entity_id = null): array {
        $validation = $this->validate_uploaded_file($file_post);

        $checksum_sha256 = $this->compute_sha256($file_post['tmp_name']);

        /* Always create a new upload (do not dedupe by checksum) */

        /* Upload directly from PHP tmp file */
        $client = new ImgBBClient();

        /* Generate a unique, non-user-controlled base name for the upload */
        $base_name = 'user-' . (int) $this->user->user_id . '-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4));

        $upload_result = $client->upload_file($file_post['tmp_name'], $base_name);

        $provider = json_decode($upload_result['provider_response_json'], true);
        $provider_data = $provider['data'] ?? [];

        $row_id = $this->imgbb_uploads_model->create((int) $this->user->user_id, [
            'checksum_sha256' => $checksum_sha256,
            'provider_asset_id' => $provider_data['id'] ?? null,
            'title' => $provider_data['title'] ?? null,
            'url_viewer' => $provider_data['url_viewer'] ?? null,
            'url' => $provider_data['url'] ?? ($upload_result['url'] ?? null),
            'display_url' => $provider_data['display_url'] ?? null,
            'thumb_url' => $provider_data['thumb']['url'] ?? $upload_result['thumb_url'] ?? null,
            'medium_url' => $provider_data['medium']['url'] ?? $upload_result['medium_url'] ?? null,
            'mime_type' => $provider_data['image']['mime'] ?? $validation['mime_type'],
            'extension' => $provider_data['image']['extension'] ?? $validation['extension'],
            'size_bytes' => isset($provider_data['size']) ? (int) $provider_data['size'] : (isset($file_post['size']) ? (int) $file_post['size'] : null),
            'width' => isset($provider_data['width']) ? (int) $provider_data['width'] : null,
            'height' => isset($provider_data['height']) ? (int) $provider_data['height'] : null,
            'delete_url' => $provider_data['delete_url'] ?? $upload_result['delete_url'] ?? null,
        ]);

        $row = db()->where('imgbb_upload_id', $row_id)->where('user_id', (int) $this->user->user_id)->getOne('imgbb_uploads');

        return [
            'status' => 200,
            'data' => array_merge($this->format_response_data_from_row($row), [
                'imgbb_upload_id' => (int) $row->imgbb_upload_id,
                'url_viewer' => $row->url_viewer,
            ]),
            'reused' => false,
        ];
    }

    /** @return array{mime_type:string,extension:?string} */
    protected function validate_uploaded_file(array $file_post): array {
        if(empty($file_post) || !isset($file_post['error']) || $file_post['error'] !== UPLOAD_ERR_OK) {
            $code = isset($file_post['error']) ? (int) $file_post['error'] : -1;
            throw new \RuntimeException('Upload failed with error code: ' . $code, 400);
        }

        $max_bytes = $this->get_max_size_bytes();
        if(isset($file_post['size']) && (int) $file_post['size'] > $max_bytes) {
            throw new \RuntimeException(sprintf('File size exceeds %dMB limit.', (int) ceil($max_bytes / 1000000)), 400);
        }

        $tmp_name = $file_post['tmp_name'] ?? null;
        if(!$tmp_name || !is_uploaded_file($tmp_name)) {
            throw new \RuntimeException('Invalid upload.', 400);
        }

        $mime_type = null;

        /* Prefer finfo functional approach to avoid Fatal Error if class is missing */
        if(function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $tmp_name);
            finfo_close($finfo);
        }

        /* Fallback 1: mime_content_type */
        if(!$mime_type && function_exists('mime_content_type')) {
            $mime_type = mime_content_type($tmp_name);
        }

        /* Fallback 2: getimagesize (for images specifically) */
        if(!$mime_type && function_exists('getimagesize')) {
            $image_info = @getimagesize($tmp_name);
            if($image_info && !empty($image_info['mime'])) {
                $mime_type = $image_info['mime'];
            }
        }

        if(!$mime_type) {
            throw new \RuntimeException('Cannot detect file type. Please enable PHP fileinfo extension in MAMP.', 400);
        }

        if(!in_array($mime_type, self::ALLOWED_MIMES, true)) {
            throw new \RuntimeException('Invalid file type. Allowed: ' . implode(', ', self::ALLOWED_MIMES), 400);
        }

        $extension = null;
        if(!empty($file_post['name'])) {
            $extension = mb_strtolower(pathinfo($file_post['name'], PATHINFO_EXTENSION)) ?: null;
        }

        return [
            'mime_type' => $mime_type,
            'extension' => $extension,
        ];
    }

    protected function compute_sha256(string $tmp_path): string {
        $hash = hash_file('sha256', $tmp_path);
        if(!$hash || mb_strlen($hash) !== 64) {
            throw new \RuntimeException('Failed to compute checksum.', 500);
        }
        return $hash;
    }

    protected function get_max_size_bytes(): int {
        $bytes = null;

        if(isset(settings()->images) && isset(settings()->images->upload_size_limit) && settings()->images->upload_size_limit) {
            $bytes = (int) settings()->images->upload_size_limit * 1000000;
        }

        if($bytes === null && isset(settings()->main->avatar_size_limit) && settings()->main->avatar_size_limit) {
            $bytes = (int) settings()->main->avatar_size_limit;
            if($bytes > 0 && $bytes < 1000) {
                $bytes = $bytes * 1000000;
            }
        }

        if($bytes === null) {
            $bytes = self::FALLBACK_MAX_SIZE_MB * 1000000;
        }

        return max(1, (int) $bytes);
    }

    protected function format_response_data_from_row($row): array {
        return [
            'url' => $row->url ?? null,
            'thumb' => ['url' => $row->thumb_url ?? null],
            'medium' => ['url' => $row->medium_url ?? null],
            'delete_url' => $row->delete_url ?? null,
        ];
    }
}
