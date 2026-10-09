<?php
namespace PCM\Payments;

if (!defined('ABSPATH')) {
    exit;
}

final class PaymentService
{
    public static function create_payment(array $data): int
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_payments';

        $wpdb->insert(
            $table,
            [
                'employee_id' => absint($data['employee_id'] ?? 0),
                'contract_id' => !empty($data['contract_id']) ? absint($data['contract_id']) : null,
                'payroll_period' => sanitize_text_field($data['payroll_period'] ?? ''),
                'amount' => (float) ($data['amount'] ?? 0),
                'payment_date' => sanitize_text_field($data['payment_date'] ?? ''),
                'tracking_number' => sanitize_text_field($data['tracking_number'] ?? wp_generate_uuid4()),
                'status' => sanitize_text_field($data['status'] ?? 'pending'),
                'description' => wp_kses_post($data['description'] ?? ''),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ],
            ['%d', '%d', '%s', '%f', '%s', '%s', '%s', '%s', '%s', '%s']
        );

        return (int) $wpdb->insert_id;
    }

    public static function get_payment(int $id): ?array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_payments';

        return $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d AND deleted_at IS NULL", $id),
            ARRAY_A
        ) ?: null;
    }

    public static function update_payment_status(int $id, string $status): bool
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_payments';

        return (bool) $wpdb->update(
            $table,
            [
                'status' => sanitize_text_field($status),
                'updated_at' => current_time('mysql'),
            ],
            ['id' => $id],
            ['%s', '%s'],
            ['%d']
        );
    }

    public static function list_pending_payments(int $limit = 100): array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_payments';
        $emp_table = $wpdb->prefix . 'pcm_employees';

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT p.*, e.first_name, e.last_name, e.employee_code FROM {$table} p
                JOIN {$emp_table} e ON p.employee_id = e.id
                WHERE p.status = 'pending' AND p.deleted_at IS NULL
                ORDER BY p.payment_date ASC LIMIT %d",
                $limit
            ),
            ARRAY_A
        ) ?: [];
    }

    public static function list_completed_payments(int $limit = 100): array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_payments';
        $emp_table = $wpdb->prefix . 'pcm_employees';

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT p.*, e.first_name, e.last_name, e.employee_code FROM {$table} p
                JOIN {$emp_table} e ON p.employee_id = e.id
                WHERE p.status = 'completed' AND p.deleted_at IS NULL
                ORDER BY p.payment_date DESC LIMIT %d",
                $limit
            ),
            ARRAY_A
        ) ?: [];
    }
}
