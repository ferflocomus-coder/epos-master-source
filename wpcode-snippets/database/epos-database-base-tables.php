<?php
/**
 * EPOS Database Base Tables
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_database_register_base_table_migrations')) {
    function epos_database_register_base_table_migrations() {
        epos_register_migration('epos_db_001_roles', function () {
            global $wpdb;

            $table_name = epos_get_table_name('roles');
            $charset    = $wpdb->get_charset_collate();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(150) NOT NULL,
                slug VARCHAR(150) NOT NULL,
                description TEXT NULL,
                status VARCHAR(50) NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_slug (slug),
                KEY idx_status (status)
            ) {$charset};";

            dbDelta($sql);
        });

        epos_register_migration('epos_db_002_system_users', function () {
            global $wpdb;

            $table_name = epos_get_table_name('system_users');
            $charset    = $wpdb->get_charset_collate();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                wp_user_id BIGINT UNSIGNED NULL,
                role_id BIGINT UNSIGNED NULL,
                full_name VARCHAR(190) NOT NULL,
                email VARCHAR(190) NOT NULL,
                phone VARCHAR(50) NULL,
                status VARCHAR(50) NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_wp_user_id (wp_user_id),
                KEY idx_role_id (role_id),
                KEY idx_email (email),
                KEY idx_status (status)
            ) {$charset};";

            dbDelta($sql);
        });

        epos_register_migration('epos_db_003_clients', function () {
            global $wpdb;

            $table_name = epos_get_table_name('clients');
            $charset    = $wpdb->get_charset_collate();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                full_name VARCHAR(190) NOT NULL,
                email VARCHAR(190) NULL,
                phone VARCHAR(50) NULL,
                address_line_1 VARCHAR(255) NULL,
                address_line_2 VARCHAR(255) NULL,
                city VARCHAR(100) NULL,
                state VARCHAR(100) NULL,
                postal_code VARCHAR(30) NULL,
                source VARCHAR(100) NULL,
                status VARCHAR(50) NOT NULL DEFAULT 'lead',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_email (email),
                KEY idx_phone (phone),
                KEY idx_status (status),
                KEY idx_source (source)
            ) {$charset};";

            dbDelta($sql);
        });

        epos_register_migration('epos_db_004_leads', function () {
            global $wpdb;

            $table_name = epos_get_table_name('leads');
            $charset    = $wpdb->get_charset_collate();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                client_id BIGINT UNSIGNED NULL,
                lead_source VARCHAR(100) NULL,
                campaign_name VARCHAR(190) NULL,
                assigned_user_id BIGINT UNSIGNED NULL,
                status VARCHAR(50) NOT NULL DEFAULT 'new',
                notes LONGTEXT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_client_id (client_id),
                KEY idx_assigned_user_id (assigned_user_id),
                KEY idx_status (status),
                KEY idx_lead_source (lead_source)
            ) {$charset};";

            dbDelta($sql);
        });

        epos_register_migration('epos_db_005_opportunities', function () {
            global $wpdb;

            $table_name = epos_get_table_name('opportunities');
            $charset    = $wpdb->get_charset_collate();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                client_id BIGINT UNSIGNED NULL,
                lead_id BIGINT UNSIGNED NULL,
                business_id BIGINT UNSIGNED NULL,
                title VARCHAR(190) NOT NULL,
                description LONGTEXT NULL,
                pipeline_stage VARCHAR(100) NULL,
                status VARCHAR(50) NOT NULL DEFAULT 'open',
                estimated_value DECIMAL(15,2) NULL DEFAULT 0.00,
                assigned_user_id BIGINT UNSIGNED NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_client_id (client_id),
                KEY idx_lead_id (lead_id),
                KEY idx_business_id (business_id),
                KEY idx_pipeline_stage (pipeline_stage),
                KEY idx_status (status),
                KEY idx_assigned_user_id (assigned_user_id)
            ) {$charset};";

            dbDelta($sql);
        });
    }
}
add_action('init', 'epos_database_register_base_table_migrations', 5);
