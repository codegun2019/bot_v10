<?php
/*
 * Copyright (c) 2025 AltumCode (https://altumcode.com/)
 *
 * This software is licensed exclusively by AltumCode and is sold only via https://altumcode.com/.
 * Unauthorized distribution, modification, or use of this software without a valid license is not permitted and may be subject to applicable legal actions.
 */

namespace Altum\Models;

defined('ALTUMCODE') || die();

class ImgbbUploads {

    public function get_by_user_and_checksum(int $user_id, string $checksum_sha256) {
        return db()
            ->where('user_id', $user_id)
            ->where('checksum_sha256', $checksum_sha256)
            ->getOne('imgbb_uploads');
    }

    public function create(int $user_id, array $data): int {
        $insert = [
            'user_id' => $user_id,
            'checksum_sha256' => $data['checksum_sha256'],
            'provider' => 'imgbb',
            'provider_asset_id' => $data['provider_asset_id'] ?? null,
            'title' => $data['title'] ?? null,
            'url_viewer' => $data['url_viewer'] ?? null,
            'url' => $data['url'] ?? null,
            'display_url' => $data['display_url'] ?? null,
            'thumb_url' => $data['thumb_url'] ?? null,
            'medium_url' => $data['medium_url'] ?? null,
            'mime_type' => $data['mime_type'] ?? null,
            'extension' => $data['extension'] ?? null,
            'size_bytes' => $data['size_bytes'] ?? null,
            'width' => $data['width'] ?? null,
            'height' => $data['height'] ?? null,
            'delete_url' => $data['delete_url'] ?? null,
            'datetime' => \Altum\Date::get(),
        ];

        return (int) db()->insert('imgbb_uploads', $insert);
    }
}
