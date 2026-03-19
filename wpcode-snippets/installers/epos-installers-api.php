<?php
/**
 * EPOS Installers API
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_installers_register_table_migration')) {
    function epos_installers_register_table_migration() {
        epos_register_migration('epos_db_010_installers', function () {
            global $wpdb;

            $table_name = epos_get_table_name('installers');
            $charset    = $wpdb->get_charset_collate();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                full_name VARCHAR(190) NOT NULL,
                company_name VARCHAR(190) NULL,
                email VARCHAR(190) NULL,
                phone VARCHAR(50) NULL,
                trade_type VARCHAR(100) NULL,
                status VARCHAR(50) NOT NULL DEFAULT 'active',
                notes LONGTEXT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_full_name (full_name),
                KEY idx_company_name (company_name),
                KEY idx_email (email),
                KEY idx_trade_type (trade_type),
                KEY idx_status (status)
            ) {$charset};";

            dbDelta($sql);
        });
    }
}
add_action('init', 'epos_installers_register_table_migration', 10);

if (!function_exists('epos_installers_api_permission')) {
    function epos_installers_api_permission() {
        return current_user_can('manage_options');
    }
}

if (!function_exists('epos_installers_table')) {
    function epos_installers_table() {
        return epos_get_table_name('installers');
    }
}

if (!function_exists('epos_installers_sanitize_payload')) {
    function epos_installers_sanitize_payload($data) {
        $data = is_array($data) ? $data : array();

        return array(
            'full_name'    => isset($data['full_name']) ? sanitize_text_field($data['full_name']) : '',
            'company_name' => isset($data['company_name']) ? sanitize_text_field($data['company_name']) : '',
            'email'        => isset($data['email']) ? sanitize_email($data['email']) : '',
            'phone'        => isset($data['phone']) ? sanitize_text_field($data['phone']) : '',
            'trade_type'   => isset($data['trade_type']) ? sanitize_text_field($data['trade_type']) : '',
            'status'       => isset($data['status']) ? sanitize_text_field($data['status']) : 'active',
            'notes'        => isset($data['notes']) ? sanitize_textarea_field($data['notes']) : '',
        );
    }
}

if (!function_exists('epos_installers_format_response')) {
    function epos_installers_format_response($success, $message, $data = null, $status = 200) {
        return new WP_REST_Response(array(
            'success' => (bool) $success,
            'message' => $message,
            'data'    => $data,
        ), $status);
    }
}

if (!function_exists('epos_installers_list')) {
    function epos_installers_list(WP_REST_Request $request) {
        global $wpdb;

        $table      = epos_installers_table();
        $page       = max(1, absint($request->get_param('page')));
        $per_page   = max(1, min(100, absint($request->get_param('per_page')) ?: 20));
        $offset     = ($page - 1) * $per_page;
        $search     = sanitize_text_field((string) $request->get_param('search'));
        $status     = sanitize_text_field((string) $request->get_param('status'));
        $trade_type = sanitize_text_field((string) $request->get_param('trade_type'));

        $where  = array('1=1');
        $params = array();

        if ($search !== '') {
            $where[] = '(full_name LIKE %s OR company_name LIKE %s OR email LIKE %s OR phone LIKE %s)';
            $like = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if ($status !== '') {
            $where[] = 'status = %s';
            $params[] = $status;
        }

        if ($trade_type !== '') {
            $where[] = 'trade_type = %s';
            $params[] = $trade_type;
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

        return epos_installers_format_response(true, 'Installers retrieved successfully.', array(
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

if (!function_exists('epos_installers_get')) {
    function epos_installers_get(WP_REST_Request $request) {
        global $wpdb;

        $table = epos_installers_table();
        $id    = absint($request['id']);

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        if (!$item) {
            return epos_installers_format_response(false, 'Installer not found.', null, 404);
        }

        return epos_installers_format_response(true, 'Installer retrieved successfully.', $item);
    }
}

if (!function_exists('epos_installers_create')) {
    function epos_installers_create(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_installers_table();
        $payload = epos_installers_sanitize_payload($request->get_json_params());

        if ($payload['full_name'] === '') {
            return epos_installers_format_response(false, 'The full_name field is required.', null, 400);
        }

        $payload['created_at'] = current_time('mysql');
        $payload['updated_at'] = current_time('mysql');

        $inserted = $wpdb->insert(
            $table,
            $payload,
            array('%s','%s','%s','%s','%s','%s','%s','%s','%s')
        );

        if ($inserted === false) {
            return epos_installers_format_response(false, 'Failed to create installer.', null, 500);
        }

        $item_id = (int) $wpdb->insert_id;
        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $item_id),
            ARRAY_A
        );

        return epos_installers_format_response(true, 'Installer created successfully.', $item, 201);
    }
}

if (!function_exists('epos_installers_update')) {
    function epos_installers_update(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_installers_table();
        $id      = absint($request['id']);
        $payload = epos_installers_sanitize_payload($request->get_json_params());

        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id)
        );

        if (!$exists) {
            return epos_installers_format_response(false, 'Installer not found.', null, 404);
        }

        if ($payload['full_name'] === '') {
            return epos_installers_format_response(false, 'The full_name field is required.', null, 400);
        }

        $payload['updated_at'] = current_time('mysql');

        $updated = $wpdb->update(
            $table,
            $payload,
            array('id' => $id),
            array('%s','%s','%s','%s','%s','%s','%s','%s'),
            array('%d')
        );

        if ($updated === false) {
            return epos_installers_format_response(false, 'Failed to update installer.', null, 500);
        }

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        return epos_installers_format_response(true, 'Installer updated successfully.', $item);
    }
}

if (!function_exists('epos_register_installers_routes')) {
    function epos_register_installers_routes() {
        register_rest_route(epos_get_rest_namespace(), '/installers', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_installers_list',
                'permission_callback' => 'epos_installers_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'epos_installers_create',
                'permission_callback' => 'epos_installers_api_permission',
            ),
        ));

        register_rest_route(epos_get_rest_namespace(), '/installers/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_installers_get',
                'permission_callback' => 'epos_installers_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'epos_installers_update',
                'permission_callback' => 'epos_installers_api_permission',
            ),
        ));
    }
}
add_action('rest_api_init', 'epos_register_installers_routes');
