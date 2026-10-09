<?php
namespace PCM\Payroll;

if (!defined('ABSPATH')) {
    exit;
}

final class PayrollService
{
    public static function create_salary_calculation(array $payload): int
    {
        global $wpdb;

        $table = $wpdb->prefix . 'pcm_salary_calculations';

        $wpdb->insert(
            $table,
            [
                'employee_id' => absint($payload['employee_id'] ?? 0),
                'contract_id' => absint($payload['contract_id'] ?? 0),
                'regulation_id' => !empty($payload['regulation_id']) ? absint($payload['regulation_id']) : null,
                'regulation_year' => sanitize_text_field($payload['regulation_year'] ?? date('Y')),
                'calculation_month' => sanitize_text_field($payload['calculation_month'] ?? date('Y-m')),
                'total_gross' => (float) ($payload['total_gross'] ?? 0),
                'total_deductions' => (float) ($payload['total_deductions'] ?? 0),
                'net_pay' => (float) ($payload['net_pay'] ?? 0),
                'status' => sanitize_text_field($payload['status'] ?? 'draft'),
                'audit_hash' => sanitize_text_field($payload['audit_hash'] ?? wp_generate_uuid4()),
                'formula_summary' => wp_json_encode($payload['formula_summary'] ?? []),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ],
            ['%d', '%d', '%d', '%s', '%s', '%f', '%f', '%f', '%s', '%s', '%s', '%s', '%s']
        );

        return (int) $wpdb->insert_id;
    }
}
