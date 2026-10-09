<?php
namespace PCM\Payroll;

if (!defined('ABSPATH')) {
    exit;
}

final class SalaryRegulation
{
    public static function get_active_regulation(string $year): ?array
    {
        global $wpdb;

        $table = $wpdb->prefix . 'pcm_salary_regulations';

        $query = $wpdb->prepare(
            "SELECT * FROM {$table} WHERE regulation_year = %s AND deleted_at IS NULL ORDER BY created_at DESC LIMIT 1",
            $year
        );

        return $wpdb->get_row($query, ARRAY_A) ?: null;
    }

    public static function get_data_for_year(string $year): array
    {
        $regulation = self::get_active_regulation($year);

        if (!$regulation) {
            return [];
        }

        $data = json_decode((string) $regulation['data'], true);
        return is_array($data) ? $data : [];
    }

    public static function set_regulation(array $payload): int
    {
        global $wpdb;

        $table = $wpdb->prefix . 'pcm_salary_regulations';

        $wpdb->insert(
            $table,
            [
                'regulation_name' => sanitize_text_field($payload['regulation_name'] ?? 'Salary Regulation'),
                'regulation_year' => sanitize_text_field($payload['regulation_year'] ?? date('Y')),
                'version' => sanitize_text_field($payload['version'] ?? '1.0'),
                'status' => sanitize_text_field($payload['status'] ?? 'draft'),
                'data' => wp_json_encode($payload['data'] ?? []),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s']
        );

        return (int) $wpdb->insert_id;
    }
}
