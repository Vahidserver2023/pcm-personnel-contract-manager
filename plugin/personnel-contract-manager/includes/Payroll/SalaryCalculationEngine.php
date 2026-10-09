<?php
namespace PCM\Payroll;

if (!defined('ABSPATH')) {
    exit;
}

final class PayItem
{
    public static function create(array $payload): int
    {
        global $wpdb;

        $table = $wpdb->prefix . 'pcm_pay_items';

        $wpdb->insert(
            $table,
            [
                'name' => sanitize_text_field($payload['name'] ?? ''),
                'code' => sanitize_text_field($payload['code'] ?? ''),
                'amount' => (float) ($payload['amount'] ?? 0),
                'calculation_type' => sanitize_text_field($payload['calculation_type'] ?? 'fixed'),
                'fixed_amount' => (float) ($payload['fixed_amount'] ?? 0),
                'percentage' => (float) ($payload['percentage'] ?? 0),
                'formula' => sanitize_text_field($payload['formula'] ?? ''),
                'is_fixed' => !empty($payload['is_fixed']) ? 1 : 0,
                'is_variable' => !empty($payload['is_variable']) ? 1 : 0,
                'eligible_for_basic_salary' => !empty($payload['eligible_for_basic_salary']) ? 1 : 0,
                'eligible_for_insurance' => !empty($payload['eligible_for_insurance']) ? 1 : 0,
                'eligible_for_tax' => !empty($payload['eligible_for_tax']) ? 1 : 0,
                'legal_basis' => sanitize_text_field($payload['legal_basis'] ?? ''),
                'regulation_id' => !empty($payload['regulation_id']) ? absint($payload['regulation_id']) : null,
                'effective_from' => !empty($payload['effective_from']) ? sanitize_text_field($payload['effective_from']) : null,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ],
            ['%s', '%s', '%f', '%s', '%f', '%f', '%s', '%d', '%d', '%d', '%d', '%d', '%s', '%d', '%s', '%s']
        );

        return (int) $wpdb->insert_id;
    }

    public static function list(): array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_pay_items';

        return $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table} WHERE deleted_at IS NULL ORDER BY id DESC LIMIT %d", 200),
            ARRAY_A
        );
    }
}
