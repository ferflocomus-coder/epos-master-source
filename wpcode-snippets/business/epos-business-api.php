<?php
/**
 * EPOS Business API
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_business_register_table_migration')) {
    function epos_business_register_table_migration() {
        epos_register_migration('epos_db_017_businesses', function () {
            global $wpdb;

            $table_name = epos_get_table_name('businesses');
            $charset    = $wpdb->get_charset_collate();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(190) NOT NULL,
                slug VARCHAR(190) NOT NULL,
                type VARCHAR(100) NULL,
                description LONGTEXT NULL,
                status VARCHAR(50) NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_slug (slug),
                KEY idx_type (type),
                KEY idx_status (status)
            ) {$charset};";

            dbDelta($sql);
        });
    }
}
add_action('init', 'epos_business_register_table_migration', 17);

if (!function_exists('epos_business_api_permission')) {
    function epos_business_api_permission() {
        return current_user_can('manage_options');
    }
}

if (!function_exists('epos_business_table')) {
    function epos_business_table() {
        return epos_get_table_name('businesses');
    }
}

if (!function_exists('epos_business_sanitize_payload')) {
    function epos_business_sanitize_payload($data) {
        $data = is_array($data) ? $data : array();

        return array(
            'name'        => isset($data['name']) ? sanitize_text_field($data['name']) : '',
            'slug'        => isset($data['slug']) ? sanitize_title($data['slug']) : '',
            'type'        => isset($data['type']) ? sanitize_text_field($data['type']) : '',
            'description' => isset($data['description']) ? sanitize_textarea_field($data['description']) : '',
            'status'      => isset($data['status']) ? sanitize_text_field($data['status']) : 'active',
        );
    }
}

if (!function_exists('epos_business_format_response')) {
    function epos_business_format_response($success, $message, $data = null, $status = 200) {
        return new WP_REST_Response(array(
            'success' => (bool) $success,
            'message' => $message,
            'data'    => $data,
        ), $status);
    }
}

if (!function_exists('epos_business_list')) {
    function epos_business_list(WP_REST_Request $request) {
        global $wpdb;

        $table    = epos_business_table();
        $page     = max(1, absint($request->get_param('page')));
        $per_page = max(1, min(100, absint($request->get_param('per_page')) ?: 20));
        $offset   = ($page - 1) * $per_page;
        $search   = sanitize_text_field((string) $request->get_param('search'));
        $status   = sanitize_text_field((string) $request->get_param('status'));

        $where  = array('1=1');
        $params = array();

        if ($search !== '') {
            $where[] = '(name LIKE %s OR slug LIKE %s OR type LIKE %s)';
            $like = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if ($status !== '') {
            $where[] = 'status = %s';
            $params[] = $status;
        }

        $where_sql = implode(' AND ', $where);
        $count_sql = "SELECT COUNT(*) FROM {$table} WHERE {$where_sql}";
        $data_sql  = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY id DESC LIMIT %d OFFSET %d";

        $count_query = !empty($params) ? $wpdb->prepare($count_sql, $params) : $count_sql;

        $data_params = $params;
        $data_params[] = $per_page;
        $data_params[] = $offset;
        $data_query = $wpdb->prepare($data_sql, $data_params);

        $total   = (int) $wpdb->get_var($count_query);
        $results = $wpdb->get_results($data_query, ARRAY_A);

        return epos_business_format_response(true, 'Businesses retrieved successfully.', array(
            'items' => $results,
            'pagination' => array(
                'page'     => $page,
                'per_page' => $per_page,
                'total'    => $total,
                'pages'    => $per_page > 0 ? (int) ceil($total / $per_page) : 1,
            ),
        ));
    }
}

if (!function_exists('epos_business_get')) {
    function epos_business_get(WP_REST_Request $request) {
        global $wpdb;

        $table = epos_business_table();
        $id    = absint($request['id']);

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        if (!$item) {
            return epos_business_format_response(false, 'Business not found.', null, 404);
        }

        return epos_business_format_response(true, 'Business retrieved successfully.', $item);
    }
}

if (!function_exists('epos_business_create')) {
    function epos_business_create(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_business_table();
        $payload = epos_business_sanitize_payload($request->get_json_params());

        if ($payload['name'] === '' || $payload['slug'] === '') {
            return epos_business_format_response(false, 'The name and slug fields are required.', null, 400);
        }

        $payload['created_at'] = current_time('mysql');
        $payload['updated_at'] = current_time('mysql');

        $inserted = $wpdb->insert(
            $table,
            $payload,
            array('%s','%s','%s','%s','%s','%s','%s')
        );

        if ($inserted === false) {
            return epos_business_format_response(false, 'Failed to create business.', null, 500);
        }

        $item_id = (int) $wpdb->insert_id;
        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $item_id),
            ARRAY_A
        );

        return epos_business_format_response(true, 'Business created successfully.', $item, 201);
    }
}

if (!function_exists('epos_business_update')) {
    function epos_business_update(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_business_table();
        $id      = absint($request['id']);
        $payload = epos_business_sanitize_payload($request->get_json_params());

        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id)
        );

        if (!$exists) {
            return epos_business_format_response(false, 'Business not found.', null, 404);
        }

        if ($payload['name'] === '' || $payload['slug'] === '') {
            return epos_business_format_response(false, 'The name and slug fields are required.', null, 400);
        }

        $payload['updated_at'] = current_time('mysql');

        $updated = $wpdb->update(
            $table,
            $payload,
            array('id' => $id),
            array('%s','%s','%s','%s','%s','%s'),
            array('%d')
        );

        if ($updated === false) {
            return epos_business_format_response(false, 'Failed to update business.', null, 500);
        }

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        return epos_business_format_response(true, 'Business updated successfully.', $item);
    }
}

if (!function_exists('epos_register_business_routes')) {
    function epos_register_business_routes() {
        register_rest_route(epos_get_rest_namespace(), '/businesses', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_business_list',
                'permission_callback' => 'epos_business_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'epos_business_create',
                'permission_callback' => 'epos_business_api_permission',
            ),
        ));

        register_rest_route(epos_get_rest_namespace(), '/businesses/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_business_get',
                'permission_callback' => 'epos_business_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'epos_business_update',
                'permission_callback' => 'epos_business_api_permission',
            ),
        ));
    }
}
add_action('rest_api_init', 'epos_register_business_routes');
