<?php
/*
 * Copyright (c) 2025 AltumCode (https://altumcode.com/)
 *
 * This software is licensed exclusively by AltumCode and is sold only via https://altumcode.com/.
 * Unauthorized distribution, modification, or use of this software without a valid license is not permitted and may be subject to applicable legal actions.
 */

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Helpers\ImgbbUploadService;

defined('ALTUMCODE') || die();

/**
 * Cache-busting comment: 2026-02-22 00:50
 */
class ImagesUpload extends Controller {

    public function index() {
        \Altum\Authentication::guard();

        /* Basic CSRF token for forms */
        \Altum\Csrf::set('global_token');

        $uploads = [];
        $total_rows = 0;
        $pagination = '';

        $view_mode = isset($_GET['view']) && in_array($_GET['view'], ['grid', 'list'], true) ? $_GET['view'] : 'list';

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['mime_type', 'extension'], ['title'], ['datetime', 'size_bytes', 'width', 'height', 'imgbb_upload_id']));
        $filters->set_default_order_by('imgbb_upload_id', 'DESC');
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Latest uploads (remote-only) */
        try {
            $search_sql = '';
            if($filters->search && $filters->search_by) {
                $search = query_clean($filters->search);
                $search_by = query_clean($filters->search_by);
                $search_sql = " AND `{$search_by}` LIKE '%{$search}%'";
            }

            $date_start = isset($_GET['date_start']) && $_GET['date_start'] ? query_clean($_GET['date_start']) : null;
            $date_end = isset($_GET['date_end']) && $_GET['date_end'] ? query_clean($_GET['date_end']) : null;
            $date_sql = '';
            if($date_start) {
                $filters->get['date_start'] = $_GET['date_start'];
                $date_sql .= " AND `datetime` >= '{$date_start} 00:00:00'";
                $filters->has_applied_filters = true;
            }
            if($date_end) {
                $filters->get['date_end'] = $_GET['date_end'];
                $date_sql .= " AND `datetime` <= '{$date_end} 23:59:59'";
                $filters->has_applied_filters = true;
            }

            $total_rows = (int) database()->query("SELECT COUNT(*) AS `total` FROM `imgbb_uploads` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()} {$search_sql} {$date_sql}")->fetch_object()->total ?? 0;

            $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('images-upload?' . $filters->get_get() . '&view=' . $view_mode . '&page=%d')));

            $result = database()->query("SELECT * FROM `imgbb_uploads` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()} {$search_sql} {$date_sql} {$filters->get_sql_order_by()} {$paginator->get_sql_limit()}");

            while($row = $result->fetch_object()) {
                $uploads[] = $row;
            }

            $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        } catch (\Exception $e) {
            if(str_contains($e->getMessage(), 'doesn\'t exist')) {
                Alerts::add_info('Database table missing. Please run the SQL migration: db/migrations/2026_02_21_000008_imgbb_uploads.sql');
            } else {
                Alerts::add_error($e->getMessage());
            }
        }

        $data = [
            'uploads' => $uploads,
            'pagination' => $pagination,
            'filters' => $filters,
            'view_mode' => $view_mode,
        ];

        $view = new \Altum\View('images-upload/index', (array) $this);
        $this->add_view_content('content', $view->run($data));
    }

    public function upload() {
        \Altum\Authentication::guard();

        if(empty($_POST)) {
            redirect('images-upload');
        }

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('images-upload');
        }

        if(empty($_FILES['image']['name'])) {
            Alerts::add_error(l('global.error_message.empty_fields'));
            redirect('images-upload');
        }

        $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        try {
            /* Sync remote-only upload */
            $service = new ImgbbUploadService($this->user);
            $result = $service->upload($_FILES['image']);

            if($is_ajax) {
                http_response_code(200);
                header('Content-Type: application/json');

                echo json_encode([
                    'success' => true,
                    'status' => 200,
                    'data' => $result['data'],
                ]);
                die();
            }

            Alerts::add_success(l('images_upload.success'));
        } catch (\Throwable $e) {
            if($is_ajax) {
                $status_code = (int) $e->getCode();
                if($status_code < 400 || $status_code > 599) $status_code = 400;

                http_response_code($status_code);
                header('Content-Type: application/json');

                echo json_encode([
                    'success' => false,
                    'status' => $status_code,
                    'error' => [
                        'code' => 'UPLOAD_ERROR',
                        'message' => $e->getMessage(),
                    ]
                ]);
                die();
            }

            Alerts::add_error(sprintf(l('images_upload.error'), $e->getMessage()));
        }

        redirect('images-upload');
    }

    public function bulk() {
        \Altum\Authentication::guard();

        $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if(empty($_POST)) {
            redirect('images-upload');
        }

        if(!\Altum\Csrf::check('global_token')) {
            if($is_ajax) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => l('global.error_message.invalid_csrf_token')
                ]);
                die();
            }

            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('images-upload');
        }

        $type = $_POST['type'] ?? '';
        $selected = $_POST['selected'] ?? [];

        if(!is_array($selected)) {
            $selected = [];
        }

        $ids = [];
        foreach($selected as $id) {
            $id = (int) $id;
            if($id) $ids[] = $id;
        }
        $ids = array_values(array_unique($ids));

        if(!$type || empty($ids)) {
            if($is_ajax) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => l('global.error_message.empty_fields')
                ]);
                die();
            }

            redirect('images-upload');
        }

        if($type === 'delete') {
            db()->where('user_id', $this->user->user_id)->where('imgbb_upload_id', $ids, 'IN')->delete('imgbb_uploads');

            if($is_ajax) {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'deleted_ids' => $ids,
                    'message' => l('images_upload.success_delete')
                ]);
                die();
            }

            Alerts::add_success(l('images_upload.success_delete'));
            redirect('images-upload');
        }

        if($type === 'copy_links') {
            $result = db()->where('user_id', $this->user->user_id)->where('imgbb_upload_id', $ids, 'IN')->get('imgbb_uploads', null, ['imgbb_upload_id', 'url']);

            $urls = [];
            foreach($result as $row) {
                if(!empty($row->url)) $urls[] = $row->url;
            }

            if($is_ajax) {
                http_response_code(200);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'urls' => $urls,
                ]);
                die();
            }

            Alerts::add_success(l('images_upload.copied'));
            redirect('images-upload');
        }

        if($is_ajax) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Invalid bulk action.'
            ]);
            die();
        }

        redirect('images-upload');
    }

    public function delete() {
        \Altum\Authentication::guard();

        $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        $imgbb_upload_id = (int) ($_POST['imgbb_upload_id'] ?? 0);

        if(!$imgbb_upload_id) {
            if($is_ajax) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => sprintf(l('images_upload.error_delete'), 'Invalid image id.')
                ]);
                die();
            }

            redirect('images-upload');
        }

        if(!\Altum\Csrf::check('global_token')) {
            if($is_ajax) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => l('global.error_message.invalid_csrf_token')
                ]);
                die();
            }

            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('images-upload');
        }

        /* Get the record */
        $row = db()->where('imgbb_upload_id', $imgbb_upload_id)->where('user_id', $this->user->user_id)->getOne('imgbb_uploads');

        if(!$row) {
            if($is_ajax) {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => sprintf(l('images_upload.error_delete'), 'Image not found.')
                ]);
                die();
            }

            redirect('images-upload');
        }

        /* Delete from database */
        db()->where('imgbb_upload_id', $imgbb_upload_id)->where('user_id', $this->user->user_id)->delete('imgbb_uploads');

        if($is_ajax) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => l('images_upload.success_delete')
            ]);
            die();
        }

        Alerts::add_success(l('images_upload.success_delete'));

        redirect('images-upload');
    }

    /**
     * @deprecated Legacy queue processor. Now using sync remote-only uploads.
     */
    public function process_queue() {
        \Altum\Authentication::guard();

        if((int) $this->user->type !== 1) {
            redirect('not-found');
        }

        Alerts::add_info('This function is deprecated. The system now uses sync remote-only uploads.');
        redirect('images-upload');
    }
}
