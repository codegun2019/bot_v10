<?php
/*
 * Copyright (c) 2025 AltumCode (https://altumcode.com/)
 *
 * This software is licensed exclusively by AltumCode and is sold only via https://altumcode.com/.
 * Unauthorized distribution, modification, or use of this software without a valid license is not permitted and may be subject to applicable legal actions.
 */

namespace Altum\Helpers;

use Altum\Models\ImagesUploadRepository;
use Altum\Uploads;

defined('ALTUMCODE') || die();

class ImagesUploadService {

    /* Single fallback value, used only if no relevant setting exists */
    const FALLBACK_MAX_SIZE_MB = 5;

    const ALLOWED_MIMES = ['image/png', 'image/jpeg', 'image/gif', 'image/webp'];

    protected $user;

    public function __construct($user) {
        $this->user = $user;
    }

    /**
     * Orchestrate upload.
     *
     * @return array{success:bool,status:int,data:array}
     */
    public function handle_upload(array $file_post, string $mode = 'sync', ?string $entity_type = null, ?int $entity_id = null, ?int $expiration = null): array {
        $mode = in_array($mode, ['sync', 'async'], true) ? $mode : 'sync';

        /* 1) Validate file (finfo + size) */
        $validation = $this->validate_uploaded_file($file_post);

        /* 2) Compute checksum */
        $checksum_sha256 = $this->compute_sha256($file_post['tmp_name']);

        /* 3) Dedupe */
        $existing_upload = ImagesUploadRepository::find_successful_by_hash((int) $this->user->user_id, $checksum_sha256);

        if($existing_upload) {
            if($entity_type && $entity_id) {
                ImagesUploadRepository::bind_entity((int) $this->user->user_id, (int) $existing_upload->image_upload_id, $entity_type, (int) $entity_id);
            }

            ImagesUploadRepository::insert_log((int) $this->user->user_id, (int) $existing_upload->image_upload_id, 'reused', 'Checksum match: reusing previous successful upload.', [
                'checksum_sha256' => $checksum_sha256,
            ]);

            /* Policy: do NOT increment_daily(success) on reuse */
            return [
                'success' => true,
                'status' => 200,
                'data' => $this->format_success_data_from_existing($existing_upload),
            ];
        }

        /* 4) Save file locally using Altum uploads */
        $local_filename = Uploads::process_upload(null, 'images', 'image', 'image_remove', $this->get_max_size_bytes());
        if(!$local_filename) {
            throw new \RuntimeException('Failed to save file locally.', 500);
        }

        $local_full_path = Uploads::get_full_path('images') . $local_filename;
        $local_size_bytes = is_file($local_full_path) ? filesize($local_full_path) : null;

        /* 5) Create local asset + queued job */
        $image_asset_id = ImagesUploadRepository::create_local_asset(
            (int) $this->user->user_id,
            null,
            $local_filename,
            (string) ($file_post['name'] ?? 'image'),
            $validation['mime_type'],
            $validation['extension'],
            $local_size_bytes ? (int) $local_size_bytes : null,
            $checksum_sha256
        );

        $image_upload_id = ImagesUploadRepository::create_job_queued(
            (int) $this->user->user_id,
            null,
            (int) $image_asset_id,
            $expiration
        );

        if($entity_type && $entity_id) {
            ImagesUploadRepository::bind_entity((int) $this->user->user_id, (int) $image_upload_id, $entity_type, (int) $entity_id);
        }

        ImagesUploadRepository::insert_log((int) $this->user->user_id, (int) $image_upload_id, 'queued');
        ImagesUploadRepository::increment_daily((int) $this->user->user_id, 'queued');

        /* 6) Async mode: just return queued */
        if($mode === 'async') {
            return [
                'success' => true,
                'status' => 202,
                'data' => [
                    'image_upload_id' => (int) $image_upload_id,
                    'state' => 'queued',
                ]
            ];
        }

        /* 7) Sync mode: upload saved local file to ImgBB */
        return $this->process_sync_upload((int) $image_upload_id, (int) $image_asset_id, $local_full_path, $local_size_bytes ? (int) $local_size_bytes : 0);
    }

    /**
     * @return array{mime_type:string,extension:?string}
     */
    protected function validate_uploaded_file(array $file_post): array {
        if(empty($file_post) || !isset($file_post['error']) || $file_post['error'] !== UPLOAD_ERR_OK) {
            $code = isset($file_post['error']) ? (int) $file_post['error'] : -1;
            throw new \RuntimeException('Upload failed with error code: ' . $code, 400);
        }

        $max_bytes = $this->get_max_size_bytes();
        if(isset($file_post['size']) && (int) $file_post['size'] > $max_bytes) {
            throw new \RuntimeException(sprintf('File size exceeds %dMB limit.', (int) ceil($max_bytes / 1024 / 1024)), 400);
        }

        $tmp_name = $file_post['tmp_name'] ?? null;
        if(!$tmp_name || !is_uploaded_file($tmp_name)) {
            throw new \RuntimeException('Invalid upload.', 400);
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($tmp_name);

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
        /*
         * Search for an existing relevant setting.
         * - ImagesUpload.php currently uses settings()->main->avatar_size_limit for uploads size.
         * - Other parts use settings()->links->*_size_limit (MB) patterns.
         *
         * Here we prioritize a dedicated setting if it exists, otherwise fallback.
         */
        $bytes = null;

        /* Potential dedicated setting: settings()->images->upload_size_limit (MB) */
        if(isset(settings()->images) && isset(settings()->images->upload_size_limit) && settings()->images->upload_size_limit) {
            $bytes = (int) settings()->images->upload_size_limit * 1000000;
        }

        /* Fallback to main avatar_size_limit if set (already used by ImagesUpload.php) */
        if($bytes === null && isset(settings()->main->avatar_size_limit) && settings()->main->avatar_size_limit) {
            $bytes = (int) settings()->main->avatar_size_limit;
            /* Some installs store bytes directly, some store MB. Heuristic: if small, treat as MB */
            if($bytes > 0 && $bytes < 1000) {
                $bytes = $bytes * 1000000;
            }
        }

        if($bytes === null) {
            $bytes = self::FALLBACK_MAX_SIZE_MB * 1000000;
        }

        return max(1, (int) $bytes);
    }

    protected function process_sync_upload(int $image_upload_id, int $image_asset_id, string $local_full_path, int $bytes_uploaded): array {
        ImagesUploadRepository::insert_log((int) $this->user->user_id, $image_upload_id, 'started');

        ImagesUploadRepository::mark_job_processing($image_upload_id);

        try {
            $client = new ImgBBClient();
            $upload_result = $client->upload_file($local_full_path);

            ImagesUploadRepository::mark_job_success($image_upload_id, $upload_result['provider_response_json'], $upload_result['delete_url']);

            /* Persist variants */
            if(!empty($upload_result['thumb_url'])) {
                ImagesUploadRepository::create_remote_variant((int) $this->user->user_id, null, $image_asset_id, 'thumb', $upload_result['thumb_url']);
            }

            if(!empty($upload_result['medium_url'])) {
                ImagesUploadRepository::create_remote_variant((int) $this->user->user_id, null, $image_asset_id, 'medium', $upload_result['medium_url']);
            }

            ImagesUploadRepository::insert_log((int) $this->user->user_id, $image_upload_id, 'success');
            ImagesUploadRepository::increment_daily((int) $this->user->user_id, 'success', 1, max(0, $bytes_uploaded));

            return [
                'success' => true,
                'status' => 200,
                'data' => [
                    'url' => $upload_result['url'],
                    'thumb' => ['url' => $upload_result['thumb_url']],
                    'medium' => ['url' => $upload_result['medium_url']],
                    /* delete_url is gated in controller */
                    'delete_url' => $upload_result['delete_url'],
                ]
            ];

        } catch (\Throwable $e) {
            ImagesUploadRepository::mark_job_failed($image_upload_id, 'api_error', $e->getMessage());
            ImagesUploadRepository::insert_log((int) $this->user->user_id, $image_upload_id, 'failed', $e->getMessage());
            ImagesUploadRepository::increment_daily((int) $this->user->user_id, 'failed');

            $code = (int) $e->getCode();
            if($code < 400 || $code > 599) $code = 500;
            throw new \RuntimeException($e->getMessage(), $code);
        }
    }

    protected function format_success_data_from_existing($existing_upload): array {
        $data = [
            'url' => null,
            'thumb' => ['url' => null],
            'medium' => ['url' => null],
            'delete_url' => $existing_upload->provider_delete_url ?? null,
        ];

        /* Original direct URL from stored provider response (ImgBB response) */
        $response = null;
        if(!empty($existing_upload->provider_response)) {
            $response = json_decode($existing_upload->provider_response, true);
        }
        $data['url'] = $response['data']['url'] ?? null;

        /* Variants */
        $variants = ImagesUploadRepository::get_asset_variants((int) $existing_upload->image_asset_id);
        foreach($variants as $v) {
            if(($v->variant ?? null) === 'thumb') $data['thumb']['url'] = $v->source_url;
            if(($v->variant ?? null) === 'medium') $data['medium']['url'] = $v->source_url;
        }

        return $data;
    }
}
