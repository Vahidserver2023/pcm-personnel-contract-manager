<?php
namespace PCM\Reports;

if (!defined('ABSPATH')) {
    exit;
}

final class PayrollReport
{
    public static function get_monthly_payroll(string $year, string $month): array
    {
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

        $totals = [
            'total_gross' => 0,
            'total_deductions' => 0,
            'total_net' => 0,
            'employee_count' => count($calculations),
        ];

        foreach ($calculations as $calc) {
            $totals['total_gross'] += (float) $calc['total_gross'];
            $totals['total_deductions'] += (float) $calc['total_deductions'];
            $totals['total_net'] += (float) $calc['net_pay'];
        }

        return [
            'month' => $month,
            'year' => $year,
            'calculations' => $calculations,
            'totals' => $totals,
        ];
    }

    public static function get_employee_annual_report(int $employee_id, string $year): array
    {
        global $wpdb;
        $calc_table = $wpdb->prefix . 'pcm_salary_calculations';
        $emp_table = $wpdb->prefix . 'pcm_employees';

        $calculations = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$calc_table}
                WHERE employee_id = %d AND regulation_year = %s AND deleted_at IS NULL
                ORDER BY calculation_month ASC",
                $employee_id,
                $year
            ),
            ARRAY_A
        );

        $employee = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$emp_table} WHERE id = %d", $employee_id),
            ARRAY_A
        );

        $totals = [
            'total_gross' => 0,
            'total_deductions' => 0,
            'total_net' => 0,
            'months_count' => count($calculations),
        ];

        foreach ($calculations as $calc) {
            $totals['total_gross'] += (float) $calc['total_gross'];
            $totals['total_deductions'] += (float) $calc['total_deductions'];
            $totals['total_net'] += (float) $calc['net_pay'];
        }

        return [
            'employee' => $employee,
            'year' => $year,
            'calculations' => $calculations,
            'totals' => $totals,
        ];
    }

    public static function get_department_payroll(int $department_id, string $year, string $month): array
    {
        global $wpdb;
        $calc_table = $wpdb->prefix . 'pcm_salary_calculations';
        $emp_table = $wpdb->prefix . 'pcm_employees';

        $calculations = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT c.*, e.first_name, e.last_name, e.employee_code FROM {$calc_table} c
                JOIN {$emp_table} e ON c.employee_id = e.id
                WHERE e.department_id = %d AND c.regulation_year = %s AND c.calculation_month = %s AND c.deleted_at IS NULL
                ORDER BY e.employee_code ASC",
                $department_id,
                $year,
                "{$year}-{$month}"
            ),
            ARRAY_A
        );

        $totals = [
            'total_gross' => 0,
            'total_deductions' => 0,
            'total_net' => 0,
            'employee_count' => count($calculations),
        ];

        foreach ($calculations as $calc) {
            $totals['total_gross'] += (float) $calc['total_gross'];
            $totals['total_deductions'] += (float) $calc['total_deductions'];
            $totals['total_net'] += (float) $calc['net_pay'];
        }

        return [
            'department_id' => $department_id,
            'month' => $month,
            'year' => $year,
            'calculations' => $calculations,
            'totals' => $totals,
        ];
    }
}
