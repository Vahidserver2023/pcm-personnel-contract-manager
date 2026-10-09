<?php
namespace PCM\API;

if (!defined('ABSPATH')) {
    exit;
}

final class RestBootstrap
{
    public function register(): void
    {
        $namespace = 'pcm/v1';

        register_rest_route($namespace, '/dashboard', [
            'methods' => 'GET',
            'callback' => [$this, 'dashboard'],
            'permission_callback' => [$this, 'permission_check'],
        ]);

        register_rest_route($namespace, '/employees', [
            'methods' => ['GET', 'POST'],
            'callback' => [$this, 'employees'],
            'permission_callback' => [$this, 'permission_check'],
        ]);

        register_rest_route($namespace, '/contracts', [
            'methods' => ['GET', 'POST'],
            'callback' => [$this, 'contracts'],
            'permission_callback' => [$this, 'permission_check'],
        ]);

        register_rest_route($namespace, '/pay-items', [
            'methods' => ['GET', 'POST'],
            'callback' => [$this, 'pay_items'],
            'permission_callback' => [$this, 'permission_check'],
        ]);

        register_rest_route($namespace, '/salary-regulations', [
            'methods' => ['GET', 'POST'],
            'callback' => [$this, 'salary_regulations'],
            'permission_callback' => [$this, 'permission_check'],
        ]);

        register_rest_route($namespace, '/salary-calculations', [
            'methods' => ['GET', 'POST'],
            'callback' => [$this, 'salary_calculations'],
            'permission_callback' => [$this, 'permission_check'],
        ]);
    }

    public function permission_check(\WP_REST_Request $request): bool
    {
        if (!is_user_logged_in()) {
            return false;
        }

        if (!wp_verify_nonce($request->get_header('X-WP-Nonce') ?: '', 'wp_rest')) {
            return false;
        }

        if (
            current_user_can('administrator') ||
            current_user_can('pcm_manage_employees') ||
            current_user_can('pcm_manage_contracts') ||
            current_user_can('pcm_manage_payroll') ||
            current_user_can('pcm_manage_salary_regulations') ||
            current_user_can('pcm_view_reports')
        ) {
            return true;
        }

        return false;
    }

    public function dashboard(\WP_REST_Request $request): \WP_REST_Response
    {
        return new \WP_REST_Response([
            'success' => true,
            'data' => [
                'total_employees' => 0,
                'active_employees' => 0,
                'active_contracts' => 0,
                'total_contracts' => 0,
                'monthly_payroll' => 0,
                'pending_payments' => 0,
                'true_labor_cost' => 0,
            ],
        ], 200);
    }

    public function employees(\WP_REST_Request $request): \WP_REST_Response
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_employees';

        $rows = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table} WHERE deleted_at IS NULL ORDER BY id DESC LIMIT %d", 50),
            ARRAY_A
        );

        return new \WP_REST_Response([
            'success' => true,
            'data' => $rows,
        ], 200);
    }

    public function contracts(\WP_REST_Request $request): \WP_REST_Response
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_contracts';

        $rows = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table} WHERE deleted_at IS NULL ORDER BY id DESC LIMIT %d", 50),
            ARRAY_A
        );

        return new \WP_REST_Response([
            'success' => true,
            'data' => $rows,
        ], 200);
    }

    public function pay_items(\WP_REST_Request $request): \WP_REST_Response
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_pay_items';

        $rows = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table} WHERE deleted_at IS NULL ORDER BY id DESC LIMIT %d", 100),
            ARRAY_A
        );

        return new \WP_REST_Response([
            'success' => true,
            'data' => $rows,
        ], 200);
    }

    public function salary_regulations(\WP_REST_Request $request): \WP_REST_Response
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_salary_regulations';

        if ($request->get_method() === 'POST') {
            $payload = $request->get_json_params();
            $result = $wpdb->insert(
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

            if (false === $result) {
                return new \WP_REST_Response([
                    'success' => false,
                    'message' => __('Failed to create salary regulation.', 'pcm'),
                ], 500);
            }

            return new \WP_REST_Response([
                'success' => true,
                'id' => (int) $wpdb->insert_id,
            ], 201);
        }

        $rows = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table} WHERE deleted_at IS NULL ORDER BY id DESC LIMIT %d", 100),
            ARRAY_A
        );

        return new \WP_REST_Response([
            'success' => true,
            'data' => $rows,
        ], 200);
    }

    public function salary_calculations(\WP_REST_Request $request): \WP_REST_Response
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_salary_calculations';

        if ($request->get_method() === 'POST') {
            $payload = $request->get_json_params();
            $result = $wpdb->insert(
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

            if (false === $result) {
                return new \WP_REST_Response([
                    'success' => false,
                    'message' => __('Failed to create salary calculation.', 'pcm'),
                ], 500);
            }

            return new \WP_REST_Response([
                'success' => true,
                'id' => (int) $wpdb->insert_id,
            ], 201);
        }

        $rows = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$table} WHERE deleted_at IS NULL ORDER BY id DESC LIMIT %d", 100),
            ARRAY_A
        );

        return new \WP_REST_Response([
            'success' => true,
            'data' => $rows,
        ], 200);
    }
}
