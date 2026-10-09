<?php
namespace PCM\API;

if (!defined('ABSPATH')) {
    exit;
}

final class ReportControllers
{
    public static function register_routes(): void
    {
        $namespace = 'pcm/v1';

        register_rest_route($namespace, '/reports/payroll', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_payroll_report'],
            'permission_callback' => [__CLASS__, 'check_report_permission'],
        ]);

        register_rest_route($namespace, '/reports/employee/(?P<id>\d+)/annual', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_employee_annual_report'],
            'permission_callback' => [__CLASS__, 'check_report_permission'],
        ]);

        register_rest_route($namespace, '/reports/department/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_department_report'],
            'permission_callback' => [__CLASS__, 'check_report_permission'],
        ]);

        register_rest_route($namespace, '/payslips/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_payslip'],
            'permission_callback' => [__CLASS__, 'check_report_permission'],
        ]);

        register_rest_route($namespace, '/payments', [
            'methods' => ['GET', 'POST'],
            'callback' => [__CLASS__, 'handle_payments'],
            'permission_callback' => [__CLASS__, 'check_payment_permission'],
        ]);

        register_rest_route($namespace, '/payments/(?P<id>\d+)/status', [
            'methods' => 'PUT',
            'callback' => [__CLASS__, 'update_payment_status'],
            'permission_callback' => [__CLASS__, 'check_payment_permission'],
        ]);

        register_rest_route($namespace, '/export/payroll', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'export_payroll'],
            'permission_callback' => [__CLASS__, 'check_export_permission'],
        ]);
    }

    public static function check_report_permission(\WP_REST_Request $request): bool
    {
        if (!is_user_logged_in()) {
            return false;
        }

        $nonce = $request->get_header('X-WP-Nonce');
        if (empty($nonce) || !wp_verify_nonce($nonce, 'wp_rest')) {
            return false;
        }

        return current_user_can('administrator') ||
               current_user_can('pcm_view_reports') ||
               current_user_can('pcm_manage_payroll');
    }

    public static function check_payment_permission(\WP_REST_Request $request): bool
    {
        if (!is_user_logged_in()) {
            return false;
        }

        $nonce = $request->get_header('X-WP-Nonce');
        if (empty($nonce) || !wp_verify_nonce($nonce, 'wp_rest')) {
            return false;
        }

        return current_user_can('administrator') ||
               current_user_can('pcm_manage_payroll');
    }

    public static function check_export_permission(\WP_REST_Request $request): bool
    {
        if (!is_user_logged_in()) {
            return false;
        }

        $nonce = $request->get_header('X-WP-Nonce');
        if (empty($nonce) || !wp_verify_nonce($nonce, 'wp_rest')) {
            return false;
        }

        return current_user_can('administrator') ||
               current_user_can('pcm_export_reports');
    }

    public static function get_payroll_report(\WP_REST_Request $request): \WP_REST_Response
    {
        $year = sanitize_text_field($request->get_param('year') ?? date('Y'));
        $month = sanitize_text_field($request->get_param('month') ?? date('m'));

        $report = \PCM\Reports\PayrollReport::get_monthly_payroll($year, $month);

        return new \WP_REST_Response([
            'success' => true,
            'data' => $report,
        ], 200);
    }

    public static function get_employee_annual_report(\WP_REST_Request $request): \WP_REST_Response
    {
        $id = (int) $request->get_param('id');
        $year = sanitize_text_field($request->get_param('year') ?? date('Y'));

        $report = \PCM\Reports\PayrollReport::get_employee_annual_report($id, $year);

        return new \WP_REST_Response([
            'success' => true,
            'data' => $report,
        ], 200);
    }

    public static function get_department_report(\WP_REST_Request $request): \WP_REST_Response
    {
        $id = (int) $request->get_param('id');
        $year = sanitize_text_field($request->get_param('year') ?? date('Y'));
        $month = sanitize_text_field($request->get_param('month') ?? date('m'));

        $report = \PCM\Reports\PayrollReport::get_department_payroll($id, $year, $month);

        return new \WP_REST_Response([
            'success' => true,
            'data' => $report,
        ], 200);
    }

    public static function get_payslip(\WP_REST_Request $request): \WP_REST_Response
    {
        $id = (int) $request->get_param('id');
        $format = sanitize_text_field($request->get_param('format') ?? 'json');

        if ($format === 'html') {
            $html = \PCM\Reports\Payslip::generate_html($id);
            return new \WP_REST_Response([
                'success' => true,
                'html' => $html,
            ], 200);
        }

        $payslip = \PCM\Reports\Payslip::generate($id);
        if (!$payslip) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => __('Payslip not found.', 'pcm'),
            ], 404);
        }

        return new \WP_REST_Response([
            'success' => true,
            'data' => $payslip,
        ], 200);
    }

    public static function handle_payments(\WP_REST_Request $request): \WP_REST_Response
    {
        if ($request->get_method() === 'POST') {
            if (!current_user_can('pcm_manage_payroll')) {
                return new \WP_REST_Response([
                    'success' => false,
                    'message' => __('Permission denied.', 'pcm'),
                ], 403);
            }

            $payload = $request->get_json_params();
            $id = \PCM\Payments\PaymentService::create_payment($payload);

            \PCM\Payroll\PayrollAudit::log([
                'action' => 'payment_created',
                'entity' => 'payment',
                'record_id' => $id,
                'new_value' => $payload,
            ]);

            return new \WP_REST_Response([
                'success' => true,
                'id' => $id,
            ], 201);
        }

        $status = sanitize_text_field($request->get_param('status') ?? 'all');
        $limit = absint($request->get_param('limit') ?? 100);

        if ($status === 'pending') {
            $payments = \PCM\Payments\PaymentService::list_pending_payments($limit);
        } elseif ($status === 'completed') {
            $payments = \PCM\Payments\PaymentService::list_completed_payments($limit);
        } else {
            global $wpdb;
            $table = $wpdb->prefix . 'pcm_payments';
            $emp_table = $wpdb->prefix . 'pcm_employees';
            $payments = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT p.*, e.first_name, e.last_name, e.employee_code FROM {$table} p
                    JOIN {$emp_table} e ON p.employee_id = e.id
                    WHERE p.deleted_at IS NULL
                    ORDER BY p.payment_date DESC LIMIT %d",
                    $limit
                ),
                ARRAY_A
            );
        }

        return new \WP_REST_Response([
            'success' => true,
            'data' => $payments,
        ], 200);
    }

    public static function update_payment_status(\WP_REST_Request $request): \WP_REST_Response
    {
        if (!current_user_can('pcm_manage_payroll')) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => __('Permission denied.', 'pcm'),
            ], 403);
        }

        $id = (int) $request->get_param('id');
        $payload = $request->get_json_params();
        $status = sanitize_text_field($payload['status'] ?? '');

        if (empty($status)) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => __('Status is required.', 'pcm'),
            ], 400);
        }

        $old_payment = \PCM\Payments\PaymentService::get_payment($id);
        \PCM\Payments\PaymentService::update_payment_status($id, $status);

        \PCM\Payroll\PayrollAudit::log([
            'action' => 'payment_status_updated',
            'entity' => 'payment',
            'record_id' => $id,
            'old_value' => $old_payment,
            'new_value' => ['status' => $status],
        ]);

        return new \WP_REST_Response([
            'success' => true,
        ], 200);
    }

    public static function export_payroll(\WP_REST_Request $request): void
    {
        if (!current_user_can('pcm_export_reports')) {
            wp_die('Permission denied.', 403);
        }

        $payload = $request->get_json_params();
        $year = sanitize_text_field($payload['year'] ?? date('Y'));
        $month = sanitize_text_field($payload['month'] ?? date('m'));

        \PCM\Payroll\PayrollAudit::log([
            'action' => 'payroll_exported',
            'entity' => 'report',
            'new_value' => ['year' => $year, 'month' => $month],
        ]);

        \PCM\Export\CSVExporter::export_payroll($year, $month);
    }
}
