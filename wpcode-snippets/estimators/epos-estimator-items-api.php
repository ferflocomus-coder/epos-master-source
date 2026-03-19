<?php
/**
 * EPOS Estimator Items API
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_estimator_items_register_table_migration')) {
    function epos_estimator_items_register_table_migration() {
        epos_register_migration('epos_db_015_estimator_items', function () {
            global $wpdb;

            $table_name = epos_get_table_name('estimator_items');
            $charset    = $wpdb->get_charset_collate();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                estimator_id BIGINT UNSIGNED NULL,
                item_type VARCHAR(100) NULL,
                item_name VARCHAR(190) NOT NULL,
                unit VARCHAR(50) NULL,
                quantity DECIMAL(12,2) NULL DEFAULT 0.00,
                unit_cost DECIMAL(15,2) NULL DEFAULT 0.00,
                total_cost DECIMAL(15,2) NULL DEFAULT 0.00,
                sort_order INT NULL DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_estimator_id (estimator_id),
                KEY idx_item_type (item_type),
                KEY idx_item_name (item_name),
                KEY idx_sort_order (sort_order)
            ) {$charset};";

            dbDelta($sql);
        });
    }
}
add_action('init', 'epos_estimator_items_register_table_migration', 15);

if (!function_exists('epos_estimator_items_api_permission')) {
    function epos_estimator_items_api_permission() {
        return current_user_can('manage_options');
    }
}

if (!function_exists('epos_estimator_items_table')) {
    function epos_estimator_items_table() {
        return epos_get_table_name('estimator_items');
    }
}

if (!function_exists('epos_estimator_items_sanitize_payload')) {
    function epos_estimator_items_sanitize_payload($data) {
        $data = is_array($data) ? $data : array();

        return array(
            'estimator_id' => isset($data['estimator_id']) ? absint($data['estimator_id']) : 0,
            'item_type'    => isset($data['item_type']) ? sanitize_text_field($data['item_type']) : '',
            'item_name'    => isset($data['item_name']) ? sanitize_text_field($data['item_name']) : '',
            'unit'         => isset($data['unit']) ? sanitize_text_field($data['unit']) : '',
            'quantity'     => isset($data['quantity']) ? (float) $data['quantity'] : 0,
            'unit_cost'    => isset($data['unit_cost']) ? (float) $data['unit_cost'] : 0,
            'total_cost'   => isset($data['total_cost']) ? (float) $data['total_cost'] : 0,
            'sort_order'   => isset($data['sort_order']) ? intval($data['sort_order']) : 0,
        );
    }
}

if (!function_exists('epos_estimator_items_format_response')) {
    function epos_estimator_items_format_response($success, $message, $data = null, $status = 200) {
        return new WP_REST_Response(array(
            'success' => (bool) $success,
            'message' => $message,
            'data'    => $data,
        ), $status);
    }
}

if (!function_exists('epos_estimator_items_list')) {
    function epos_estimator_items_list(WP_REST_Request $request) {
        global $wpdb;

        $table        = epos_estimator_items_table();
        $page         = max(1, absint($request->get_param('page')));
        $per_page     = max(1, min(100, absint($request->get_param('per_page')) ?: 20));
        $offset       = ($page - 1) * $per_page;
        $search       = sanitize_text_field((string) $request->get_param('search'));
        $item_type    = sanitize_text_field((string) $request->get_param('item_type'));
        $estimator_id = absint($request->get_param('estimator_id'));

        $where  = array('1=1');
        $params = array();

        if ($search !== '') {
            $where[] = '(item_name LIKE %s OR unit LIKE %s)';
            $like = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $like;
            $params[] = $like;
        }

        if ($item_type !== '') {
            $where[] = 'item_type = %s';
            $params[] = $item_type;
        }

        if ($estimator_id > 0) {
            $where[] = 'estimator_id = %d';
            $params[] = $estimator_id;
        }

        $where_sql = implode(' AND ', $where);
        $count_sql = "SELECT COUNT(*) FROM {$table} WHERE {$where_sql}";
        $data_sql  = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY sort_order ASC, id ASC LIMIT %d OFFSET %d";

        $count_query = !empty($params) ? $wpdb->prepare($count_sql, $params) : $count_sql;

        $data_params = $params;
        $data_params[] = $per_page;
        $data_params[] = $offset;
        $data_query = $wpdb->prepare($data_sql, $data_params);

        $total   = (int) $wpdb->get_var($count_query);
        $results = $wpdb->get_results($data_query, ARRAY_A);

        return epos_estimator_items_format_response(true, 'Estimator items retrieved successfully.', array(
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

if (!function_exists('epos_estimator_items_get')) {
    function epos_estimator_items_get(WP_REST_Request $request) {
        global $wpdb;

        $table = epos_estimator_items_table();
        $id    = absint($request['id']);

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        if (!$item) {
            return epos_estimator_items_format_response(false, 'Estimator item not found.', null, 404);
        }

        return epos_estimator_items_format_response(true, 'Estimator item retrieved successfully.', $item);
    }
}

if (!function_exists('epos_estimator_items_create')) {
    function epos_estimator_items_create(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_estimator_items_table();
        $payload = epos_estimator_items_sanitize_payload($request->get_json_params());

        if ($payload['item_name'] === '') {
            return epos_estimator_items_format_response(false, 'The item_name field is required.', null, 400);
        }

        $payload['estimator_id'] = $payload['estimator_id'] > 0 ? $payload['estimator_id'] : null;
        $payload['created_at']   = current_time('mysql');
        $payload['updated_at']   = current_time('mysql');

        $inserted = $wpdb->insert(
            $table,
            $payload,
            array('%d','%s','%s','%s','%f','%f','%f','%d','%s','%s')
        );

        if ($inserted === false) {
            return epos_estimator_items_format_response(false, 'Failed to create estimator item.', null, 500);
        }

        $item_id = (int) $wpdb->insert_id;
        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $item_id),
            ARRAY_A
        );

        return epos_estimator_items_format_response(true, 'Estimator item created successfully.', $item, 201);
    }
}

if (!function_exists('epos_estimator_items_update')) {
    function epos_estimator_items_update(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_estimator_items_table();
        $id      = absint($request['id']);
        $payload = epos_estimator_items_sanitize_payload($request->get_json_params());

        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id)
        );

        if (!$exists) {
            return epos_estimator_items_format_response(false, 'Estimator item not found.', null, 404);
        }

        if ($payload['item_name'] === '') {
            return epos_estimator_items_format_response(false, 'The item_name field is required.', null, 400);
        }

        $payload['estimator_id'] = $payload['estimator_id'] > 0 ? $payload['estimator_id'] : null;
        $payload['updated_at']   = current_time('mysql');

        $updated = $wpdb->update(
            $table,
            $payload,
            array('id' => $id),
            array('%d','%s','%s','%s','%f','%f','%f','%d','%s'),
            array('%d')
        );

        if ($updated === false) {
            return epos_estimator_items_format_response(false, 'Failed to update estimator item.', null, 500);
        }

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        return epos_estimator_items_format_response(true, 'Estimator item updated successfully.', $item);
    }
}

if (!function_exists('epos_register_estimator_items_routes')) {
    function epos_register_estimator_items_routes() {
        register_rest_route(epos_get_rest_namespace(), '/estimator-items', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_estimator_items_list',
                'permission_callback' => 'epos_estimator_items_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'epos_estimator_items_create',
                'permission_callback' => 'epos_estimator_items_api_permission',
            ),
        ));

        register_rest_route(epos_get_rest_namespace(), '/estimator-items/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_estimator_items_get',
                'permission_callback' => 'epos_estimator_items_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'epos_estimator_items_update',
                'permission_callback' => 'epos_estimator_items_api_permission',
            ),
        ));
    }
}
add_action('rest_api_init', 'epos_register_estimator_items_routes');
