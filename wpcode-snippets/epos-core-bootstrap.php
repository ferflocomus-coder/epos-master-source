<?php
/**
 * EPOS Core Bootstrap
 *
 * WPCode-compatible bootstrap snippet for Evolution Power OS.
 *
 * Responsibilities:
 * - Define shared constants and helpers
 * - Register the base REST namespace
 * - Register and run migrations safely
 * - Expose a bootstrap status endpoint
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('EPOS_REST_NAMESPACE')) {
    define('EPOS_REST_NAMESPACE', 'ep/v1');
}

if (!isset($GLOBALS['epos_migration_registry']) || !is_array($GLOBALS['epos_migration_registry'])) {
    $GLOBALS['epos_migration_registry'] = array();
}

if (!function_exists('epos_get_rest_namespace')) {
    function epos_get_rest_namespace() {
        return EPOS_REST_NAMESPACE;
    }
}

if (!function_exists('epos_get_table_name')) {
    function epos_get_table_name($table) {
        global $wpdb;

        $table = is_string($table) ? trim($table) : '';
        $table = strtolower($table);
        $table = preg_replace('/[^a-z0-9_]/', '', $table);

        if ($table === '') {
            return '';
        }

        return $wpdb->prefix . 'ep_' . $table;
    }
}

if (!function_exists('epos_get_executed_migrations')) {
    function epos_get_executed_migrations() {
        $executed = get_option('epos_executed_migrations', array());

        return is_array($executed) ? $executed : array();
    }
}

if (!function_exists('epos_set_executed_migrations')) {
    function epos_set_executed_migrations($migrations) {
        $migrations = is_array($migrations) ? $migrations : array();
        update_option('epos_executed_migrations', array_values(array_unique($migrations)), false);
    }
}

if (!function_exists('epos_register_migration')) {
    function epos_register_migration($key, $callback) {
        if (!is_string($key) || $key === '') {
            return false;
        }

        if (!is_callable($callback)) {
            return false;
        }

        if (!isset($GLOBALS['epos_migration_registry']) || !is_array($GLOBALS['epos_migration_registry'])) {
            $GLOBALS['epos_migration_registry'] = array();
        }

        $GLOBALS['epos_migration_registry'][$key] = $callback;

        return true;
    }
}

if (!function_exists('epos_get_registered_migrations')) {
    function epos_get_registered_migrations() {
        if (!isset($GLOBALS['epos_migration_registry']) || !is_array($GLOBALS['epos_migration_registry'])) {
            return array();
        }

        return $GLOBALS['epos_migration_registry'];
    }
}

if (!function_exists('epos_run_migrations')) {
    function epos_run_migrations() {
        $registered = epos_get_registered_migrations();
        $executed   = epos_get_executed_migrations();

        if (empty($registered)) {
            return array(
                'executed' => 0,
                'pending'  => 0,
            );
        }

        $executed_now = 0;

        foreach ($registered as $key => $callback) {
            if (in_array($key, $executed, true)) {
                continue;
            }

            call_user_func($callback);
            $executed[] = $key;
            $executed_now++;
        }

        if ($executed_now > 0) {
            epos_set_executed_migrations($executed);
        }

        $pending = 0;
        foreach (array_keys($registered) as $key) {
            if (!in_array($key, $executed, true)) {
                $pending++;
            }
        }

        return array(
            'executed' => $executed_now,
            'pending'  => $pending,
        );
    }
}

if (!function_exists('epos_get_bootstrap_status')) {
    function epos_get_bootstrap_status() {
        $registered = epos_get_registered_migrations();
        $executed   = epos_get_executed_migrations();

        $pending = 0;
        foreach (array_keys($registered) as $key) {
            if (!in_array($key, $executed, true)) {
                $pending++;
            }
        }

        return array(
            'success'                   => true,
            'namespace'                 => epos_get_rest_namespace(),
            'executed_migrations_count' => count($executed),
            'pending_migrations_count'  => $pending,
            'timestamp'                 => current_time('mysql'),
        );
    }
}

if (!function_exists('epos_core_register_default_migrations')) {
    function epos_core_register_default_migrations() {
        epos_register_migration('epos_core_migration_001_bootstrap_option_init', function () {
            if (get_option('epos_executed_migrations', null) === null) {
                add_option('epos_executed_migrations', array(), '', false);
            }
        });
    }
}
add_action('init', 'epos_core_register_default_migrations', 1);

if (!function_exists('epos_core_run_migrations_on_init')) {
    function epos_core_run_migrations_on_init() {
        epos_run_migrations();
    }
}
add_action('init', 'epos_core_run_migrations_on_init', 20);

if (!function_exists('epos_rest_permission_callback')) {
    function epos_rest_permission_callback() {
        return current_user_can('manage_options');
    }
}

if (!function_exists('epos_register_core_rest_routes')) {
    function epos_register_core_rest_routes() {
        register_rest_route(epos_get_rest_namespace(), '/system/bootstrap-status', array(
            'methods'             => 'GET',
            'callback'            => function () {
                return rest_ensure_response(epos_get_bootstrap_status());
            },
            'permission_callback' => 'epos_rest_permission_callback',
        ));
    }
}
add_action('rest_api_init', 'epos_register_core_rest_routes');
