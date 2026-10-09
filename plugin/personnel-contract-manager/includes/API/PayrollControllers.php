<?php
namespace PCM\API;

if (!defined('ABSPATH')) {
    exit;
}

final class PayrollControllers
{
    public static function register_routes(): void
    {
        $namespace = 'pcm/v1';

        register_rest_route($namespace, '/employees', [
            'methods' => ['GET', 'POST'],
            'callback' => [__CLASS__, 'handle_employees'],
            'permission_callback' => [__CLASS__, 'check_permission'],
        ]);

        register_rest_route($namespace, '/employees/(?P<id>\d+)', [
            'methods' => ['GET', 'PUT', 'DELETE'],
            'callback' => [__CLASS__, 'handle_single_employee'],
            'permission_callback' => [__CLASS__, 'check_permission'],
        ]);

        register_rest_route($namespace, '/contracts', [
            'methods' => ['GET', 'POST'],
            'callback' => [__CLASS__, 'handle_contracts'],
            'permission_callback' => [__CLASS__, 'check_permission'],
        ]);

        register_rest_route($namespace, '/contracts/(?P<id>\d+)/renew', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'renew_contract'],
            'permission_callback' => [__CLASS__, 'check_permission'],
        ]);

        register_rest_route($namespace, '/contracts/(?P<id>\d+)/terminate', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'terminate_contract'],
            'permission_callback' => [__CLASS__, 'check_permission'],
        ]);

        register_rest_route($namespace, '/departments', [
            'methods' => ['GET', 'POST'],
            'callback' => [__CLASS__, 'handle_departments'],
            'permission_callback' => [__CLASS__, 'check_permission'],
        ]);

        register_rest_route($namespace, '/positions', [
            'methods' => ['GET', 'POST'],
            'callback' => [__CLASS__, 'handle_positions'],
            'permission_callback' => [__CLASS__, 'check_permission'],
        ]);
    }

    public static function check_permission(\WP_REST_Request $request): bool
    {
        if (!is_user_logged_in()) {
            return false;
        }

        $nonce = $request->get_header('X-WP-Nonce');
        if (empty($nonce) || !wp_verify_nonce($nonce, 'wp_rest')) {
            return false;
        }

        return current_user_can('administrator') ||
               current_user_can('pcm_manage_employees') ||
               current_user_can('pcm_manage_contracts');
    }

    public static function handle_employees(\WP_REST_Request $request): \WP_REST_Response
    {
        if ($request->get_method() === 'POST') {
            if (!current_user_can('pcm_manage_employees')) {
                return new \WP_REST_Response([
                    'success' => false,
                    'message' => __('Permission denied.', 'pcm'),
                ], 403);
            }

            $payload = $request->get_json_params();
            $id = \PCM\Models\Employee::create($payload);

            \PCM\Payroll\PayrollAudit::log([
                'action' => 'employee_created',
                'entity' => 'employee',
                'record_id' => $id,
                'new_value' => $payload,
            ]);

            return new \WP_REST_Response([
                'success' => true,
                'id' => $id,
            ], 201);
        }

        $employees = \PCM\Models\Employee::list_all();
        return new \WP_REST_Response([
            'success' => true,
            'data' => $employees,
        ], 200);
    }

    public static function handle_single_employee(\WP_REST_Request $request): \WP_REST_Response
    {
        $id = (int) $request->get_param('id');

        if ($request->get_method() === 'GET') {
            $employee = \PCM\Models\Employee::get($id);
            if (!$employee) {
                return new \WP_REST_Response([
                    'success' => false,
                    'message' => __('Employee not found.', 'pcm'),
                ], 404);
            }

            return new \WP_REST_Response([
                'success' => true,
                'data' => $employee,
            ], 200);
        }

        if ($request->get_method() === 'PUT') {
            if (!current_user_can('pcm_manage_employees')) {
                return new \WP_REST_Response([
                    'success' => false,
                    'message' => __('Permission denied.', 'pcm'),
                ], 403);
            }

            $old_data = \PCM\Models\Employee::get($id);
            $payload = $request->get_json_params();
            \PCM\Models\Employee::update($id, $payload);

            \PCM\Payroll\PayrollAudit::log([
                'action' => 'employee_updated',
                'entity' => 'employee',
                'record_id' => $id,
                'old_value' => $old_data,
                'new_value' => $payload,
            ]);

            return new \WP_REST_Response([
                'success' => true,
            ], 200);
        }

        if ($request->get_method() === 'DELETE') {
            if (!current_user_can('pcm_manage_employees')) {
                return new \WP_REST_Response([
                    'success' => false,
                    'message' => __('Permission denied.', 'pcm'),
                ], 403);
            }

            \PCM\Models\Employee::delete($id);

            \PCM\Payroll\PayrollAudit::log([
                'action' => 'employee_deleted',
                'entity' => 'employee',
                'record_id' => $id,
            ]);

            return new \WP_REST_Response([
                'success' => true,
            ], 200);
        }

        return new \WP_REST_Response([
            'success' => false,
            'message' => __('Method not allowed.', 'pcm'),
        ], 405);
    }

    public static function handle_contracts(\WP_REST_Request $request): \WP_REST_Response
    {
        if ($request->get_method() === 'POST') {
            if (!current_user_can('pcm_manage_contracts')) {
                return new \WP_REST_Response([
                    'success' => false,
                    'message' => __('Permission denied.', 'pcm'),
                ], 403);
            }

            $payload = $request->get_json_params();
            $id = \PCM\Models\Contract::create($payload);

            \PCM\Payroll\PayrollAudit::log([
                'action' => 'contract_created',
                'entity' => 'contract',
                'record_id' => $id,
                'new_value' => $payload,
            ]);

            return new \WP_REST_Response([
                'success' => true,
                'id' => $id,
            ], 201);
        }

        $contracts = \PCM\Models\Contract::list_all();
        return new \WP_REST_Response([
            'success' => true,
            'data' => $contracts,
        ], 200);
    }

    public static function renew_contract(\WP_REST_Request $request): \WP_REST_Response
    {
        if (!current_user_can('pcm_manage_contracts')) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => __('Permission denied.', 'pcm'),
            ], 403);
        }

        $id = (int) $request->get_param('id');
        $payload = $request->get_json_params();
        $new_id = \PCM\Models\Contract::renew($id, $payload);

        \PCM\Payroll\PayrollAudit::log([
            'action' => 'contract_renewed',
            'entity' => 'contract',
            'record_id' => $new_id,
            'old_value' => ['previous_contract_id' => $id],
            'new_value' => $payload,
        ]);

        return new \WP_REST_Response([
            'success' => true,
            'id' => $new_id,
        ], 201);
    }

    public static function terminate_contract(\WP_REST_Request $request): \WP_REST_Response
    {
        if (!current_user_can('pcm_manage_contracts')) {
            return new \WP_REST_Response([
                'success' => false,
                'message' => __('Permission denied.', 'pcm'),
            ], 403);
        }

        $id = (int) $request->get_param('id');
        \PCM\Models\Contract::terminate($id);

        \PCM\Payroll\PayrollAudit::log([
            'action' => 'contract_terminated',
            'entity' => 'contract',
            'record_id' => $id,
        ]);

        return new \WP_REST_Response([
            'success' => true,
        ], 200);
    }

    public static function handle_departments(\WP_REST_Request $request): \WP_REST_Response
    {
        if ($request->get_method() === 'POST') {
            if (!current_user_can('administrator')) {
                return new \WP_REST_Response([
                    'success' => false,
                    'message' => __('Permission denied.', 'pcm'),
                ], 403);
            }

            $payload = $request->get_json_params();
            $id = \PCM\Models\Department::create($payload);

            return new \WP_REST_Response([
                'success' => true,
                'id' => $id,
            ], 201);
        }

        $departments = \PCM\Models\Department::list_all();
        return new \WP_REST_Response([
            'success' => true,
            'data' => $departments,
        ], 200);
    }

    public static function handle_positions(\WP_REST_Request $request): \WP_REST_Response
    {
        if ($request->get_method() === 'POST') {
            if (!current_user_can('administrator')) {
                return new \WP_REST_Response([
                    'success' => false,
                    'message' => __('Permission denied.', 'pcm'),
                ], 403);
            }

            $payload = $request->get_json_params();
            $id = \PCM\Models\Position::create($payload);

            return new \WP_REST_Response([
                'success' => true,
                'id' => $id,
            ], 201);
        }

        $positions = \PCM\Models\Position::list_all();
        return new \WP_REST_Response([
            'success' => true,
            'data' => $positions,
        ], 200);
    }
}
