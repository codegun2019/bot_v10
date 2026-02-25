<?php
/*
 * Copyright (c) 2025 AltumCode (https://altumcode.com/)
 *
 * This software is licensed exclusively by AltumCode and is sold only via https://altumcode.com/.
 * Unauthorized distribution, modification, or use of this software without a valid license is not permitted and may be subject to applicable legal actions.
 */

namespace Altum\Helpers;

defined('ALTUMCODE') || die();

class ImgBBClient {

    private string $api_key;
    private string $endpoint;
    private int $timeout;

    public function __construct(?string $api_key = null, string $endpoint = 'https://api.imgbb.com/1/upload', int $timeout = 30) {
        $api_key = $api_key ?? (defined('IMGBB_API_KEY') ? IMGBB_API_KEY : null);

        if(!$api_key) {
            throw new \RuntimeException('ImgBB API key is missing.');
        }

        $this->api_key = $api_key;
        $this->endpoint = $endpoint;
        $this->timeout = $timeout;
    }

    /**
     * Upload a local file to ImgBB.
     *
     * @return array{url:string, thumb_url:?string, medium_url:?string, delete_url:?string, provider_response_json:string}
     */
    public function upload_file(string $file_path, ?string $name = null, ?int $expiration = null): array {
        if(!is_file($file_path) || !is_readable($file_path)) {
            throw new \RuntimeException('File is not readable.');
        }

        $query = [
            'key' => $this->api_key,
        ];

        if($expiration !== null) {
            $query['expiration'] = $expiration;
        }

        $url = $this->endpoint . '?' . http_build_query($query);

        $post_fields = [
            'image' => new \CURLFile($file_path),
        ];

        if($name) {
            $post_fields['name'] = $name;
        }

        $ch = curl_init($url);

        $ssl_verify = defined('IMGBB_SSL_VERIFY') ? (bool) IMGBB_SSL_VERIFY : true;

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $post_fields,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => min(10, $this->timeout),
            CURLOPT_SSL_VERIFYPEER => $ssl_verify,
            CURLOPT_SSL_VERIFYHOST => $ssl_verify ? 2 : 0,
        ]);

        $raw = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($raw === false) {
            throw new \RuntimeException('cURL error: ' . $curl_error);
        }

        $decoded = json_decode($raw, true);

        if(!is_array($decoded)) {
            throw new \RuntimeException('Invalid response from ImgBB.');
        }

        if($http_code >= 400 || empty($decoded['success'])) {
            $message = $decoded['error']['message'] ?? ('HTTP ' . $http_code);
            throw new \RuntimeException('ImgBB API error: ' . $message);
        }

        $data = $decoded['data'] ?? [];

        return [
            'url' => (string) ($data['url'] ?? ''),
            'thumb_url' => $data['thumb']['url'] ?? null,
            'medium_url' => $data['medium']['url'] ?? null,
            'delete_url' => $data['delete_url'] ?? null,
            'provider_response_json' => $raw,
        ];
    }
}
