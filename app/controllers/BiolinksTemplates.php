<?php
/*
 * Copyright (c) 2025 AltumCode (https://altumcode.com/)
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

use Altum\Models\Domain;
use Altum\Response;

defined('ALTUMCODE') || die();

class BiolinksTemplates extends Controller {

    public function index() {

        if(!settings()->links->biolinks_is_enabled || !settings()->links->biolinks_templates_is_enabled) {
            redirect('not-found');
        }

        \Altum\Authentication::guard();

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters([], ['name'], ['biolink_template_id', 'order', 'name', 'total_usage', 'datetime']));
        $filters->set_default_order_by('order', 'ASC');
        $filters->set_default_results_per_page($this->user->preferences->default_results_per_page ?? settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `biolinks_templates` WHERE `is_enabled` = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('biolinks-templates?' . $filters->get_get() . '&page=%d')));

        /* Get the links list for the project */
        $result = database()->query("
            SELECT 
                *
            FROM 
                `biolinks_templates`
            WHERE 
                `is_enabled` = 1
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
            {$paginator->get_sql_limit()}
        ");

        /* Iterate over the links */
        $biolinks_templates = [];

        while($row = $result->fetch_object()) {
            $biolinks_templates[] = $row;
        }

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Get domains */
        $domains = (new Domain())->get_available_domains_by_user($this->user);

        /* Create Link Modal */
        $view = new \Altum\View('links/create_link_modals', (array) $this);
        \Altum\Event::add_content($view->run(['domains' => $domains]), 'modals');

        /* Get user favorites */
        $favorites = json_decode($this->user->preferences->favorite_templates ?? '[]', true) ?: [];

        /* Prepare the view */
        $data = [
            'biolinks_templates' => $biolinks_templates,
            'domains'            => $domains,
            'pagination'         => $pagination,
            'filters'            => $filters,
            'favorites'          => $favorites,
        ];

        $view = new \Altum\View('biolinks-templates/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function toggle_favorite() {
        \Altum\Authentication::guard();

        if(!settings()->links->biolinks_is_enabled || !settings()->links->biolinks_templates_is_enabled) {
            Response::json('', 'error');
        }

        /* Check CSRF token */
        if(!\Altum\Csrf::check('global_token')) {
            Response::json('Invalid CSRF token', 'error');
        }

        /* Get JSON input */
        $json = file_get_contents('php://input');
        $post = json_decode($json, true);

        $template_id = (int) ($post['template_id'] ?? 0);
        $action = $post['action'] ?? '';

        if(!$template_id || !in_array($action, ['add', 'remove'])) {
            Response::json('Invalid request', 'error');
        }

        /* Verify template exists */
        $template = database()->query("SELECT `biolink_template_id` FROM `biolinks_templates` WHERE `biolink_template_id` = {$template_id} AND `is_enabled` = 1")->fetch_object();
        
        if(!$template) {
            Response::json('Template not found', 'error');
        }

        /* Get current preferences from database (not from cached user object) */
        $user_result = database()->query("SELECT `preferences` FROM `users` WHERE `user_id` = {$this->user->user_id}")->fetch_object();
        $preferences = json_decode($user_result->preferences ?? '{}', true);
        
        if(!is_array($preferences)) {
            $preferences = [];
        }
        
        /* Get current favorites */
        $favorites = $preferences['favorite_templates'] ?? [];
        
        if(!is_array($favorites)) {
            $favorites = [];
        }

        /* Add or remove favorite */
        if($action === 'add') {
            if(!in_array($template_id, $favorites)) {
                $favorites[] = $template_id;
            }
        } else {
            $favorites = array_filter($favorites, function($id) use ($template_id) {
                return (int)$id != (int)$template_id;
            });
            $favorites = array_values($favorites); // Re-index array
        }

        /* Update preferences - preserve all existing preferences */
        $preferences['favorite_templates'] = $favorites;
        
        database()->where('user_id', $this->user->user_id)->update('users', [
            'preferences' => json_encode($preferences),
        ]);

        /* Clear cache */
        cache()->deleteItemsByTag('user_id=' . $this->user->user_id);

        Response::simple_json(['status' => true, 'message' => $action === 'add' ? 'Added to favorites' : 'Removed from favorites']);
    }

    public function favorites() {
        if(!settings()->links->biolinks_is_enabled || !settings()->links->biolinks_templates_is_enabled) {
            redirect('not-found');
        }

        \Altum\Authentication::guard();

        /* Since favorites are now stored in localStorage, 
           we'll load all templates and let JavaScript filter them.
           Alternatively, we could pass favorites via GET parameter from JavaScript */
        $favorites_param = $_GET['favorites'] ?? '';
        $favorites = [];
        
        if($favorites_param) {
            // Decode base64 and parse JSON
            $decoded = base64_decode($favorites_param, true);
            if($decoded !== false) {
                $favorites = json_decode($decoded, true);
                if(!is_array($favorites)) {
                    $favorites = [];
                }
            }
            
            // Ensure all IDs are integers and valid
            $favorites = array_map('intval', $favorites);
            $favorites = array_filter($favorites, function($id) {
                return $id > 0;
            });
            // Re-index array after filter
            $favorites = array_values($favorites);
        }

        if(empty($favorites)) {
            $biolinks_templates = [];
        } else {
            /* Get favorite templates - Use raw SQL with sanitized values */
            // Sanitize all IDs
            $sanitized_ids = array_map(function($id) {
                return (int) $id;
            }, $favorites);
            
            // Create IN clause with sanitized IDs
            $ids_string = implode(',', $sanitized_ids);
            
            // Create ORDER BY FIELD to maintain order
            $field_order = implode(',', $sanitized_ids);
            
            $result = database()->query("
                SELECT 
                    *
                FROM 
                    `biolinks_templates`
                WHERE 
                    `is_enabled` = 1
                    AND `biolink_template_id` IN ({$ids_string})
                ORDER BY 
                    FIELD(`biolink_template_id`, {$field_order})
            ");

            $biolinks_templates = [];
            if($result) {
                while($row = $result->fetch_object()) {
                    $biolinks_templates[] = $row;
                }
            }
        }

        /* Get domains */
        $domains = (new Domain())->get_available_domains_by_user($this->user);

        /* Create Link Modal */
        $view = new \Altum\View('links/create_link_modals', (array) $this);
        \Altum\Event::add_content($view->run(['domains' => $domains]), 'modals');

        /* Prepare the view */
        $data = [
            'biolinks_templates' => $biolinks_templates,
            'domains'            => $domains,
            'favorites'          => $favorites,
        ];

        $view = new \Altum\View('biolinks-templates/favorites', (array) $this);

        $this->add_view_content('content', $view->run($data));
    }

}


