<?php
namespace PCM\Models;

if (!defined('ABSPATH')) {
    exit;
}

final class Employee
{
    public static function create(array $data): int
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_employees';

        $wpdb->insert(
            $table,
            [
                'employee_code' => sanitize_text_field($data['employee_code'] ?? ''),
                'first_name' => sanitize_text_field($data['first_name'] ?? ''),
                'last_name' => sanitize_text_field($data['last_name'] ?? ''),
                'father_name' => sanitize_text_field($data['father_name'] ?? ''),
                'national_id' => $data['national_id'] ? self::encrypt($data['national_id']) : null,
                'birth_date' => sanitize_text_field($data['birth_date'] ?? ''),
                'gender' => sanitize_text_field($data['gender'] ?? ''),
                'marital_status' => sanitize_text_field($data['marital_status'] ?? ''),
                'nationality' => sanitize_text_field($data['nationality'] ?? ''),
                'mobile' => sanitize_text_field($data['mobile'] ?? ''),
                'phone' => sanitize_text_field($data['phone'] ?? ''),
                'email' => sanitize_email($data['email'] ?? ''),
                'address' => wp_kses_post($data['address'] ?? ''),
                'department_id' => !empty($data['department_id']) ? absint($data['department_id']) : null,
                'position_id' => !empty($data['position_id']) ? absint($data['position_id']) : null,
                'manager_id' => !empty($data['manager_id']) ? absint($data['manager_id']) : null,
                'employment_type' => sanitize_text_field($data['employment_type'] ?? ''),
                'start_date' => sanitize_text_field($data['start_date'] ?? ''),
                'end_date' => sanitize_text_field($data['end_date'] ?? ''),
                'status' => sanitize_text_field($data['status'] ?? 'active'),
                'bank_details' => $data['bank_details'] ? self::encrypt(wp_json_encode($data['bank_details'])) : null,
                'avatar_url' => esc_url_raw($data['avatar_url'] ?? ''),
                'description' => wp_kses_post($data['description'] ?? ''),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ]
        );

        return (int) $wpdb->insert_id;
    }

    public static function get(int $id): ?array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_employees';

        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d AND deleted_at IS NULL", $id),
            ARRAY_A
        );

        if (!$row) {
            return null;
        }

        $row['national_id'] = $row['national_id'] ? self::decrypt($row['national_id']) : null;
        $row['bank_details'] = $row['bank_details'] ? json_decode(self::decrypt($row['bank_details']), true) : null;

        return $row;
    }

    public static function update(int $id, array $data): bool
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_employees';

        $update_data = [
            'updated_at' => current_time('mysql'),
        ];

        if (!empty($data['first_name'])) {
            $update_data['first_name'] = sanitize_text_field($data['first_name']);
        }
        if (!empty($data['last_name'])) {
            $update_data['last_name'] = sanitize_text_field($data['last_name']);
        }
        if (!empty($data['email'])) {
            $update_data['email'] = sanitize_email($data['email']);
        }
        if (!empty($data['status'])) {
            $update_data['status'] = sanitize_text_field($data['status']);
        }

        return (bool) $wpdb->update($table, $update_data, ['id' => $id]);
    }

    public static function list_all(int $limit = 100): array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_employees';

        return $wpdb->get_results(
            $wpdb->prepare("SELECT id, employee_code, first_name, last_name, email, department_id, position_id, status, created_at FROM {$table} WHERE deleted_at IS NULL ORDER BY id DESC LIMIT %d", $limit),
            ARRAY_A
        ) ?: [];
    }

    public static function delete(int $id): bool
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_employees';

        return (bool) $wpdb->update(
            $table,
            ['deleted_at' => current_time('mysql')],
            ['id' => $id]
        );
    }

    private static function encrypt(string $data): string
    {
        return base64_encode($data);
    }

    private static function decrypt(string $data): string
    {
        return base64_decode($data);
    }
}
