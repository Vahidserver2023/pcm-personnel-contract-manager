<?php
namespace PCM\Models;

if (!defined('ABSPATH')) {
    exit;
}

final class Department
{
    public static function create(array $data): int
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_departments';

        $wpdb->insert(
            $table,
            [
                'name' => sanitize_text_field($data['name'] ?? ''),
                'code' => sanitize_text_field($data['code'] ?? ''),
                'description' => wp_kses_post($data['description'] ?? ''),
                'parent_id' => !empty($data['parent_id']) ? absint($data['parent_id']) : null,
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
        $table = $wpdb->prefix . 'pcm_departments';

        return $wpdb->get_results(
            "SELECT * FROM {$table} WHERE deleted_at IS NULL ORDER BY name ASC",
            ARRAY_A
        ) ?: [];
    }
}
