<?php
namespace PCM\Database;

if (!defined('ABSPATH')) {
    exit;
}

final class Schema
{
    public static function create_tables(): void
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_prefix = $wpdb->prefix . 'pcm_';

        $sql = [];

        $sql[] = "CREATE TABLE IF NOT EXISTS {$table_prefix}employees (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            employee_code VARCHAR(100) NOT NULL,
            first_name VARCHAR(255) NOT NULL,
            last_name VARCHAR(255) NOT NULL,
            father_name VARCHAR(255) DEFAULT NULL,
            national_id VARCHAR(255) DEFAULT NULL,
            birth_date DATE DEFAULT NULL,
            gender VARCHAR(20) DEFAULT NULL,
            marital_status VARCHAR(50) DEFAULT NULL,
            nationality VARCHAR(120) DEFAULT NULL,
            mobile VARCHAR(50) DEFAULT NULL,
            phone VARCHAR(50) DEFAULT NULL,
            email VARCHAR(255) DEFAULT NULL,
            address LONGTEXT DEFAULT NULL,
            department_id BIGINT UNSIGNED DEFAULT NULL,
            position_id BIGINT UNSIGNED DEFAULT NULL,
            manager_id BIGINT UNSIGNED DEFAULT NULL,
            employment_type VARCHAR(50) DEFAULT NULL,
            start_date DATE DEFAULT NULL,
            end_date DATE DEFAULT NULL,
            status VARCHAR(50) DEFAULT 'active',
            bank_details LONGTEXT DEFAULT NULL,
            avatar_url VARCHAR(255) DEFAULT NULL,
            description LONGTEXT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at DATETIME DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY employee_code (employee_code)
        ) {$charset_collate};";

        $sql[] = "CREATE TABLE IF NOT EXISTS {$table_prefix}departments (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            code VARCHAR(100) DEFAULT NULL,
            description LONGTEXT DEFAULT NULL,
            parent_id BIGINT UNSIGNED DEFAULT NULL,
            status VARCHAR(50) DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at DATETIME DEFAULT NULL,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        $sql[] = "CREATE TABLE IF NOT EXISTS {$table_prefix}positions (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            code VARCHAR(100) DEFAULT NULL,
            department_id BIGINT UNSIGNED DEFAULT NULL,
            description LONGTEXT DEFAULT NULL,
            status VARCHAR(50) DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at DATETIME DEFAULT NULL,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        $sql[] = "CREATE TABLE IF NOT EXISTS {$table_prefix}contracts (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            contract_number VARCHAR(120) NOT NULL,
            employee_id BIGINT UNSIGNED NOT NULL,
            contract_type VARCHAR(100) DEFAULT NULL,
            title VARCHAR(255) DEFAULT NULL,
            start_date DATE DEFAULT NULL,
            end_date DATE DEFAULT NULL,
            contract_amount DECIMAL(18,2) DEFAULT 0,
            monthly_salary DECIMAL(18,2) DEFAULT 0,
            department_id BIGINT UNSIGNED DEFAULT NULL,
            position_id BIGINT UNSIGNED DEFAULT NULL,
            terms LONGTEXT DEFAULT NULL,
            description LONGTEXT DEFAULT NULL,
            status VARCHAR(50) DEFAULT 'draft',
            previous_contract_id BIGINT UNSIGNED DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at DATETIME DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY contract_number (contract_number)
        ) {$charset_collate};";

        $sql[] = "CREATE TABLE IF NOT EXISTS {$table_prefix}salary_regulations (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            regulation_name VARCHAR(255) NOT NULL,
            regulation_year VARCHAR(20) NOT NULL,
            version VARCHAR(50) DEFAULT '1.0',
            status VARCHAR(50) DEFAULT 'draft',
            data LONGTEXT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at DATETIME DEFAULT NULL,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        $sql[] = "CREATE TABLE IF NOT EXISTS {$table_prefix}pay_items (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            code VARCHAR(100) NOT NULL,
            amount DECIMAL(18,2) DEFAULT 0,
            calculation_type VARCHAR(50) DEFAULT 'fixed',
            fixed_amount DECIMAL(18,2) DEFAULT 0,
            percentage DECIMAL(12,4) DEFAULT 0,
            formula VARCHAR(255) DEFAULT NULL,
            is_fixed TINYINT(1) DEFAULT 0,
            is_variable TINYINT(1) DEFAULT 0,
            eligible_for_basic_salary TINYINT(1) DEFAULT 0,
            eligible_for_insurance TINYINT(1) DEFAULT 0,
            eligible_for_tax TINYINT(1) DEFAULT 0,
            legal_basis VARCHAR(255) DEFAULT NULL,
            regulation_id BIGINT UNSIGNED DEFAULT NULL,
            effective_from DATE DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at DATETIME DEFAULT NULL,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        $sql[] = "CREATE TABLE IF NOT EXISTS {$table_prefix}salary_calculations (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            employee_id BIGINT UNSIGNED NOT NULL,
            contract_id BIGINT UNSIGNED NOT NULL,
            regulation_id BIGINT UNSIGNED DEFAULT NULL,
            regulation_year VARCHAR(20) DEFAULT NULL,
            calculation_month VARCHAR(20) DEFAULT NULL,
            total_gross DECIMAL(18,2) DEFAULT 0,
            total_deductions DECIMAL(18,2) DEFAULT 0,
            net_pay DECIMAL(18,2) DEFAULT 0,
            status VARCHAR(50) DEFAULT 'draft',
            audit_hash VARCHAR(255) DEFAULT NULL,
            formula_summary LONGTEXT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at DATETIME DEFAULT NULL,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        $sql[] = "CREATE TABLE IF NOT EXISTS {$table_prefix}salary_calculation_items (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            calculation_id BIGINT UNSIGNED NOT NULL,
            pay_item_id BIGINT UNSIGNED DEFAULT NULL,
            name VARCHAR(255) NOT NULL,
            base_value DECIMAL(18,2) DEFAULT 0,
            rate DECIMAL(12,4) DEFAULT 0,
            quantity DECIMAL(12,4) DEFAULT 0,
            formula VARCHAR(255) DEFAULT NULL,
            amount DECIMAL(18,2) DEFAULT 0,
            regulation_id BIGINT UNSIGNED DEFAULT NULL,
            regulation_year VARCHAR(20) DEFAULT NULL,
            legal_basis VARCHAR(255) DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        $sql[] = "CREATE TABLE IF NOT EXISTS {$table_prefix}payments (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            employee_id BIGINT UNSIGNED NOT NULL,
            contract_id BIGINT UNSIGNED DEFAULT NULL,
            payroll_period VARCHAR(20) DEFAULT NULL,
            amount DECIMAL(18,2) DEFAULT 0,
            payment_date DATE DEFAULT NULL,
            tracking_number VARCHAR(120) DEFAULT NULL,
            status VARCHAR(50) DEFAULT 'pending',
            description LONGTEXT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at DATETIME DEFAULT NULL,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        $sql[] = "CREATE TABLE IF NOT EXISTS {$table_prefix}documents (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            employee_id BIGINT UNSIGNED DEFAULT NULL,
            document_type VARCHAR(100) DEFAULT NULL,
            file_name VARCHAR(255) DEFAULT NULL,
            file_path VARCHAR(500) DEFAULT NULL,
            mime_type VARCHAR(120) DEFAULT NULL,
            file_size BIGINT UNSIGNED DEFAULT 0,
            status VARCHAR(50) DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at DATETIME DEFAULT NULL,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        $sql[] = "CREATE TABLE IF NOT EXISTS {$table_prefix}audit_logs (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT UNSIGNED DEFAULT NULL,
            action VARCHAR(100) DEFAULT NULL,
            entity VARCHAR(150) DEFAULT NULL,
            record_id BIGINT UNSIGNED DEFAULT NULL,
            log_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            ip_address VARCHAR(255) DEFAULT NULL,
            user_agent VARCHAR(500) DEFAULT NULL,
            old_value LONGTEXT DEFAULT NULL,
            new_value LONGTEXT DEFAULT NULL,
            formula VARCHAR(500) DEFAULT NULL,
            regulation_id BIGINT UNSIGNED DEFAULT NULL,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        $sql[] = "CREATE TABLE IF NOT EXISTS {$table_prefix}settings (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            option_name VARCHAR(255) NOT NULL,
            option_value LONGTEXT DEFAULT NULL,
            option_group VARCHAR(150) DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY option_name (option_name)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
}
