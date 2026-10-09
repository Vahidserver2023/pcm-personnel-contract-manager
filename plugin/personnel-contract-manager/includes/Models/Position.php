<?php
namespace PCM\Models;

if (!defined('ABSPATH')) {
    exit;
}

final class Position
{
    public static function create(array $data): int
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_positions';

        $wpdb->insert(
            $table,
            [
                'name' => sanitize_text_field($data['name'] ?? ''),
                'code' => sanitize_text_field($data['code'] ?? ''),
                'department_id' => !empty($data['department_id']) ? absint($data['department_id']) : null,
                'description' => wp_kses_post($data['description'] ?? ''),
                'status' => sanitize_text_field($data['status'] ?? 'active'),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ]
        );

        return (int) $wpdb->insert_id;
    }

    public static function list_all(): array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_positions';

        return $wpdb->get_results(
            "SELECT * FROM {$table} WHERE deleted_at IS NULL ORDER BY name ASC",
            ARRAY_A
        ) ?: [];
    }
}
