<?php
namespace PCM\Payroll;

if (!defined('ABSPATH')) {
    exit;
}

final class PayrollAudit
{
    public static function log(array $data): void
    {
        global $wpdb;

        $table = $wpdb->prefix . 'pcm_audit_logs';

        $wpdb->insert(
            $table,
            [
                'user_id' => !empty($data['user_id']) ? absint($data['user_id']) : get_current_user_id(),
                'action' => sanitize_text_field($data['action'] ?? 'Salary Calculation'),
                'entity' => sanitize_text_field($data['entity'] ?? 'salary_calculation'),
                'record_id' => !empty($data['record_id']) ? absint($data['record_id']) : 0,
                'log_date' => current_time('mysql'),
                'ip_address' => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? ''),
                'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? ''),
                'old_value' => wp_json_encode($data['old_value'] ?? []),
                'new_value' => wp_json_encode($data['new_value'] ?? []),
                'formula' => sanitize_text_field($data['formula'] ?? ''),
                'regulation_id' => !empty($data['regulation_id']) ? absint($data['regulation_id']) : null,
            ],
            ['%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d']
        );
    }
}
