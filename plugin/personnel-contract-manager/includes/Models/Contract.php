<?php
namespace PCM\Models;

if (!defined('ABSPATH')) {
    exit;
}

final class Contract
{
    public static function create(array $data): int
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_contracts';

        $wpdb->insert(
            $table,
            [
                'contract_number' => sanitize_text_field($data['contract_number'] ?? ''),
                'employee_id' => absint($data['employee_id'] ?? 0),
                'contract_type' => sanitize_text_field($data['contract_type'] ?? ''),
                'title' => sanitize_text_field($data['title'] ?? ''),
                'start_date' => sanitize_text_field($data['start_date'] ?? ''),
                'end_date' => sanitize_text_field($data['end_date'] ?? ''),
                'contract_amount' => (float) ($data['contract_amount'] ?? 0),
                'monthly_salary' => (float) ($data['monthly_salary'] ?? 0),
                'department_id' => !empty($data['department_id']) ? absint($data['department_id']) : null,
                'position_id' => !empty($data['position_id']) ? absint($data['position_id']) : null,
                'terms' => wp_kses_post($data['terms'] ?? ''),
                'description' => wp_kses_post($data['description'] ?? ''),
                'status' => sanitize_text_field($data['status'] ?? 'draft'),
                'previous_contract_id' => !empty($data['previous_contract_id']) ? absint($data['previous_contract_id']) : null,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ]
        );

        return (int) $wpdb->insert_id;
    }

    public static function get(int $id): ?array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_contracts';

        return $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d AND deleted_at IS NULL", $id),
            ARRAY_A
        ) ?: null;
    }

    public static function renew(int $id, array $data): int
    {
        $old_contract = self::get($id);
        if (!$old_contract) {
            return 0;
        }

        $new_data = array_merge($old_contract, $data);
        $new_data['previous_contract_id'] = $id;
        $new_data['status'] = 'active';
        unset($new_data['id'], $new_data['created_at'], $new_data['updated_at'], $new_data['deleted_at']);

        return self::create($new_data);
    }

    public static function terminate(int $id): bool
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_contracts';

        return (bool) $wpdb->update(
            $table,
            ['status' => 'terminated', 'updated_at' => current_time('mysql')],
            ['id' => $id]
        );
    }

    public static function get_expiring_contracts(int $days = 30): array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_contracts';
        $date = date('Y-m-d', strtotime("+{$days} days"));

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE end_date <= %s AND end_date > NOW() AND status = 'active' AND deleted_at IS NULL",
                $date
            ),
            ARRAY_A
        ) ?: [];
    }

    public static function list_all(int $limit = 100): array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_contracts';

        return $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table} WHERE deleted_at IS NULL ORDER BY id DESC LIMIT %d", $limit),
            ARRAY_A
        ) ?: [];
    }
}
