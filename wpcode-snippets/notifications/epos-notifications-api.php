<?php
/**
 * EPOS Notifications API
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_notifications_register_table_migration')) {
    function epos_notifications_register_table_migration() {
        epos_register_migration('epos_db_013_notifications', function () {
            global $wpdb;

            $table_name = epos_get_table_name('notifications');
            $charset    = $wpdb->get_charset_collate();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id BIGINT UNSIGNED NULL,
                type VARCHAR(100) NULL,
                title VARCHAR(190) NOT NULL,
                message LONGTEXT NULL,
                entity_type VARCHAR(100) NULL,
                entity_id BIGINT UNSIGNED NULL,
                is_read TINYINT(1) NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_user_id (user_id),
                KEY idx_type (type),
                KEY idx_entity_type (entity_type),
                KEY idx_entity_id (entity_id),
                KEY idx_is_read (is_read)
            ) {$charset};";

            dbDelta($sql);
        });
    }
}
add_action('init', 'epos_notifications_register_table_migration', 13);

if (!function_exists('epos_notifications_api_permission')) {
    function epos_notifications_api_permission() {
        return current_user_can('manage_options');
    }
}

if (!function_exists('epos_notifications_table')) {
    function epos_notifications_table() {
        return epos_get_table_name('notifications');
    }
}

if (!function_exists('epos_notifications_sanitize_payload')) {
    function epos_notifications_sanitize_payload($data) {
        $data = is_array($data) ? $data : array();

        return array(
            'user_id'     => isset($data['user_id']) ? absint($data['user_id']) : 0,
            'type'        => isset($data['type']) ? sanitize_text_field($data['type']) : '',
            'title'       => isset($data['title']) ? sanitize_text_field($data['title']) : '',
            'message'     => isset($data['message']) ? sanitize_textarea_field($data['message']) : '',
            'entity_type' => isset($data['entity_type']) ? sanitize_text_field($data['entity_type']) : '',
            'entity_id'   => isset($data['entity_id']) ? absint($data['entity_id']) : 0,
            'is_read'     => isset($data['is_read']) ? (int) !!$data['is_read'] : 0,
        );
    }
}

if (!function_exists('epos_notifications_format_response')) {
    function epos_notifications_format_response($success, $message, $data = null, $status = 200) {
        return new WP_REST_Response(array(
            'success' => (bool) $success,
            'message' => $message,
            'data'    => $data,
        ), $status);
    }
}

if (!function_exists('epos_notifications_list')) {
    function epos_notifications_list(WP_REST_Request $request) {
        global $wpdb;

        $table       = epos_notifications_table();
        $page        = max(1, absint($request->get_param('page')));
        $per_page    = max(1, min(100, absint($request->get_param('per_page')) ?: 20));
        $offset      = ($page - 1) * $per_page;
        $user_id     = absint($request->get_param('user_id'));
        $type        = sanitize_text_field((string) $request->get_param('type'));
        $entity_type = sanitize_text_field((string) $request->get_param('entity_type'));
        $entity_id   = absint($request->get_param('entity_id'));
        $is_read_raw = $request->get_param('is_read');

        $where  = array('1=1');
        $params = array();

        if ($user_id > 0) {
            $where[] = 'user_id = %d';
            $params[] = $user_id;
        }

        if ($type !== '') {
            $where[] = 'type = %s';
            $params[] = $type;
        }

        if ($entity_type !== '') {
            $where[] = 'entity_type = %s';
            $params[] = $entity_type;
        }

        if ($entity_id > 0) {
            $where[] = 'entity_id = %d';
            $params[] = $entity_id;
        }

        if ($is_read_raw !== null && $is_read_raw !== '') {
            $where[] = 'is_read = %d';
            $params[] = ((int) !!$is_read_raw);
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

        return epos_notifications_format_response(true, 'Notifications retrieved successfully.', array(
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

if (!function_exists('epos_notifications_get')) {
    function epos_notifications_get(WP_REST_Request $request) {
        global $wpdb;

        $table = epos_notifications_table();
        $id    = absint($request['id']);

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        if (!$item) {
            return epos_notifications_format_response(false, 'Notification not found.', null, 404);
        }

        return epos_notifications_format_response(true, 'Notification retrieved successfully.', $item);
    }
}

if (!function_exists('epos_notifications_create')) {
    function epos_notifications_create(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_notifications_table();
        $payload = epos_notifications_sanitize_payload($request->get_json_params());

        if ($payload['title'] === '') {
            return epos_notifications_format_response(false, 'The title field is required.', null, 400);
        }

        $payload['user_id']    = $payload['user_id'] > 0 ? $payload['user_id'] : null;
        $payload['entity_id']  = $payload['entity_id'] > 0 ? $payload['entity_id'] : null;
        $payload['created_at'] = current_time('mysql');
        $payload['updated_at'] = current_time('mysql');

        $inserted = $wpdb->insert(
            $table,
            $payload,
            array('%d','%s','%s','%s','%s','%d','%d','%s','%s')
        );

        if ($inserted === false) {
            return epos_notifications_format_response(false, 'Failed to create notification.', null, 500);
        }

        $item_id = (int) $wpdb->insert_id;
        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $item_id),
            ARRAY_A
        );

        return epos_notifications_format_response(true, 'Notification created successfully.', $item, 201);
    }
}

if (!function_exists('epos_notifications_update')) {
    function epos_notifications_update(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_notifications_table();
        $id      = absint($request['id']);
        $payload = epos_notifications_sanitize_payload($request->get_json_params());

        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id)
        );

        if (!$exists) {
            return epos_notifications_format_response(false, 'Notification not found.', null, 404);
        }

        if ($payload['title'] === '') {
            return epos_notifications_format_response(false, 'The title field is required.', null, 400);
        }

        $payload['user_id']    = $payload['user_id'] > 0 ? $payload['user_id'] : null;
        $payload['entity_id']  = $payload['entity_id'] > 0 ? $payload['entity_id'] : null;
        $payload['updated_at'] = current_time('mysql');

        $updated = $wpdb->update(
            $table,
            $payload,
            array('id' => $id),
            array('%d','%s','%s','%s','%s','%d','%d','%s'),
            array('%d')
        );

        if ($updated === false) {
            return epos_notifications_format_response(false, 'Failed to update notification.', null, 500);
        }

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        return epos_notifications_format_response(true, 'Notification updated successfully.', $item);
    }
}

if (!function_exists('epos_register_notifications_routes')) {
    function epos_register_notifications_routes() {
        register_rest_route(epos_get_rest_namespace(), '/notifications', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_notifications_list',
                'permission_callback' => 'epos_notifications_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'epos_notifications_create',
                'permission_callback' => 'epos_notifications_api_permission',
            ),
        ));

        register_rest_route(epos_get_rest_namespace(), '/notifications/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_notifications_get',
                'permission_callback' => 'epos_notifications_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'epos_notifications_update',
                'permission_callback' => 'epos_notifications_api_permission',
            ),
        ));
    }
}
add_action('rest_api_init', 'epos_register_notifications_routes');
