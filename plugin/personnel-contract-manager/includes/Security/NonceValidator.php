<?php
namespace PCM\Security;

if (!defined('ABSPATH')) {
    exit;
}

final class CapabilityManager
{
    public static function register_roles_and_capabilities(): void
    {
        $roles = [
            'administrator' => [
                'pcm_manage_employees',
                'pcm_manage_contracts',
                'pcm_manage_payroll',
                'pcm_manage_salary_regulations',
                'pcm_view_reports',
                'pcm_export_reports',
                'pcm_manage_documents',
                'pcm_manage_audit_logs',
                'pcm_manage_settings',
            ],
            'hr_manager' => [
                'pcm_manage_employees',
                'pcm_manage_contracts',
                'pcm_view_reports',
                'pcm_manage_documents',
            ],
            'contract_manager' => [
                'pcm_manage_contracts',
                'pcm_view_reports',
            ],
            'payroll_manager' => [
                'pcm_manage_payroll',
                'pcm_manage_salary_regulations',
                'pcm_view_reports',
            ],
            'viewer' => [
                'pcm_view_reports',
            ],
        ];

        foreach ($roles as $role_name => $caps) {
            $role = get_role($role_name);

            if (!$role && $role_name !== 'administrator') {
                add_role($role_name, ucfirst(str_replace('_', ' ', $role_name)), []);
                $role = get_role($role_name);
            }

            if ($role) {
                foreach ($caps as $cap) {
                    $role->add_cap($cap);
                }
            }
        }

        $admin = get_role('administrator');
        if ($admin) {
            foreach ($roles['administrator'] as $cap) {
                $admin->add_cap($cap);
            }
        }
    }
}
