<?php
namespace PCM\Export;

if (!defined('ABSPATH')) {
    exit;
}

final class CSVExporter
{
    public static function export_payroll(string $year, string $month, string $filename = ''): void
    {
        if (empty($filename)) {
            $filename = "payroll-{$year}-{$month}.csv";
        }

        global $wpdb;
        $calc_table = $wpdb->prefix . 'pcm_salary_calculations';
        $emp_table = $wpdb->prefix . 'pcm_employees';

        $calculations = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT c.*, e.first_name, e.last_name, e.employee_code FROM {$calc_table} c
                JOIN {$emp_table} e ON c.employee_id = e.id
                WHERE c.regulation_year = %s AND c.calculation_month = %s AND c.deleted_at IS NULL
                ORDER BY e.employee_code ASC",
                $year,
                "{$year}-{$month}"
            ),
            ARRAY_A
        );

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . esc_attr($filename) . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['کد کارمندی', 'نام', 'نام خانوادگی', 'حقوق ناخالص', 'کسورات', 'خالص پرداختی', 'وضعیت'], ',', '"');

        foreach ($calculations as $calc) {
            fputcsv($output, [
                $calc['employee_code'],
                $calc['first_name'],
                $calc['last_name'],
                (float) $calc['total_gross'],
                (float) $calc['total_deductions'],
                (float) $calc['net_pay'],
                $calc['status'],
            ], ',', '"');
        }

        fclose($output);
        exit;
    }

    public static function export_employees(string $filename = 'employees.csv'): void
    {
        global $wpdb;
        $table = $wpdb->prefix . 'pcm_employees';

        $employees = $wpdb->get_results(
            "SELECT id, employee_code, first_name, last_name, email, mobile, department_id, position_id, status, created_at FROM {$table} WHERE deleted_at IS NULL ORDER BY employee_code ASC",
            ARRAY_A
        );

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . esc_attr($filename) . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['کد', 'نام', 'نام خانوادگی', 'ایمیل', 'موبایل', 'بخش', 'سمت', 'وضعیت', 'تاریخ ایجاد'], ',', '"');

        foreach ($employees as $emp) {
            fputcsv($output, [
                $emp['employee_code'],
                $emp['first_name'],
                $emp['last_name'],
                $emp['email'],
                $emp['mobile'],
                $emp['department_id'],
                $emp['position_id'],
                $emp['status'],
                $emp['created_at'],
            ], ',', '"');
        }

        fclose($output);
        exit;
    }
}
