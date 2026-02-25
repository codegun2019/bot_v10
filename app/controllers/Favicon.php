<?php
/*
 * Copyright (c) 2026 AltumCode (https://altumcode.com/)
 *
 * This software is licensed exclusively by AltumCode and is sold only via https://altumcode.com/.
 * Unauthorized distribution, modification, or use of this software without a valid license is not permitted and may be subject to applicable legal actions.
 *
 * 🌍 View all other existing AltumCode projects via https://altumcode.com/
 * 📧 Get in touch for support or general queries via https://altumcode.com/contact
 * 📤 Download the latest version via https://altumcode.com/downloads
 *
 * 🐦 X/Twitter: https://x.com/AltumCode
 * 📘 Facebook: https://facebook.com/altumcode
 * 📸 Instagram: https://instagram.com/altumcode
 */

namespace Altum\Controllers;

defined('ALTUMCODE') || die();

class Favicon extends Controller {

    public function index() {
        function clean_domain($input_string) {
            $input_string = strtolower(trim(preg_replace('/^[a-z]+:\/\/|[\/\?#].*$/i', '', (string)$input_string)));

            /* Handle bracketed IPv6 like [2001:db8::1]:8080 */
            if(preg_match('/^\[([0-9a-f:.]+)\](?::\d+)?$/i', $input_string, $match)) {
                $input_string = $match[1];
            } else {
                /* Strip userinfo */
                $input_string = preg_replace('/^[^@]+@/', '', $input_string);

                /* Strip :port only when it's not IPv6 */
                if(substr_count($input_string, ':') === 1 && preg_match('/:\d+$/', $input_string)) {
                    $input_string = preg_replace('/:\d+$/', '', $input_string);
                }
            }

            $input_string = trim($input_string, '.');
            if($input_string === '') return null;

            /* If it's an IP (v4/v6) -> allow it (we'll serve fallback) */
            if(filter_var($input_string, FILTER_VALIDATE_IP)) {
                return $input_string;
            }

            if(!function_exists('idn_to_ascii')) return null;

            $ascii_domain = idn_to_ascii($input_string, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
            if(!$ascii_domain || !preg_match('/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)(?:\.(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?))*$/', $ascii_domain)) return null;

            return idn_to_utf8($ascii_domain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
        }

        /* Take the input from the URL */
        $host = isset($this->params[0]) ? $this->params[0] : null;

        if(!$host || mb_strlen($host) > 250) {
            throw_404();
        }

        $host = clean_domain($host);

        if(!$host) {
            throw_404();
        }

        /* Caching directory */
        $cache_path = UPLOADS_PATH . 'cache/icons';

        /* Create directory if not existing */
        if(!is_dir($cache_path)) mkdir($cache_path, 0777, true);

        $cache_ttl_seconds = 30 * 24 * 60 * 60; // 30 days
        $negative_ttl_seconds = 24 * 60 * 60; // 1 day

        $cache_key = hash('sha256', $host);
        $icon_path = $cache_path . '/' . $cache_key . '.ico';
        $miss_path = $cache_path . '/' . $cache_key . '.miss';

        /* Put your fallback file here */
        $fallback_path = ASSETS_PATH . 'images/favicon-service-default.ico';

        /* If host is IP -> always serve fallback (no DDG request) */
        if(filter_var($host, FILTER_VALIDATE_IP)) {
            if(is_file($fallback_path)) {
                header('Content-Type: image/x-icon');
                header('Cache-Control: public, max-age=' . $negative_ttl_seconds);
                readfile($fallback_path);
                exit;
            }

            http_response_code(204);
            exit;
        }

        /* Serve cached icon */
        if(is_file($icon_path) && (time() - filemtime($icon_path)) < $cache_ttl_seconds) {
            header('Content-Type: image/x-icon');
            header('Cache-Control: public, max-age=' . $cache_ttl_seconds);
            readfile($icon_path);
            exit;
        }

        /* Negative cache: serve fallback (avoid hammering DDG) */
        if(is_file($miss_path) && (time() - filemtime($miss_path)) < $negative_ttl_seconds) {
            if(is_file($fallback_path)) {
                header('Content-Type: image/x-icon');
                header('Cache-Control: public, max-age=' . $negative_ttl_seconds);
                readfile($fallback_path);
                exit;
            }

            http_response_code(204);
            exit;
        }

        $ddg_url = 'https://icons.duckduckgo.com/ip3/' . $host . '.ico';

        \Unirest\Request::curlOpts([
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_CONNECTTIMEOUT_MS => 2000,
            CURLOPT_TIMEOUT_MS => 2500,
        ]);

        $response = \Unirest\Request::get($ddg_url, ['User-Agent' => 'favicon-proxy/1.0']);

        $status_code = isset($response->code) ? (int) $response->code : 0;
        $body = $response->raw_body;

        /* Save the received icon and return it */
        if($status_code === 200 && $body !== '' && strlen($body) < 1024 * 1024) {
            file_put_contents($icon_path, $body, LOCK_EX);
            if(is_file($miss_path)) unlink($miss_path);

            header('Content-Type: image/x-icon');
            header('Cache-Control: public, max-age=' . $cache_ttl_seconds);
            echo $body;
            exit;
        }

        /* Set a missed path for a 404 response on the favicon  to try later */
        file_put_contents($miss_path, '1', LOCK_EX);

        /* Return a fallback default icon */
        if(is_file($fallback_path)) {
            header('Content-Type: image/x-icon');
            header('Cache-Control: public, max-age=' . $negative_ttl_seconds);
            readfile($fallback_path);
            exit;
        }

        http_response_code(204);
        exit;
    }

}
