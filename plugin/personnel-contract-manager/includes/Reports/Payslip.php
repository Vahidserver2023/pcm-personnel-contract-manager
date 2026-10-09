<?php
namespace PCM\Reports;

if (!defined('ABSPATH')) {
    exit;
}

final class Payslip
{
    public static function generate(int $calculation_id): ?array
    {
        global $wpdb;
        $calc_table = $wpdb->prefix . 'pcm_salary_calculations';
        $item_table = $wpdb->prefix . 'pcm_salary_calculation_items';
        $emp_table = $wpdb->prefix . 'pcm_employees';
        $contract_table = $wpdb->prefix . 'pcm_contracts';

        $calculation = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$calc_table} WHERE id = %d", $calculation_id),
            ARRAY_A
        );

        if (!$calculation) {
            return null;
        }

        $employee = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$emp_table} WHERE id = %d", $calculation['employee_id']),
            ARRAY_A
        );

        $contract = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$contract_table} WHERE id = %d", $calculation['contract_id']),
            ARRAY_A
        );

        $items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$item_table} WHERE calculation_id = %d ORDER BY id ASC",
                $calculation_id
            ),
            ARRAY_A
        );

        return [
            'employee' => $employee,
            'contract' => $contract,
            'calculation' => $calculation,
            'items' => $items,
        ];
    }

    public static function generate_html(int $calculation_id): string
    {
        $payslip = self::generate($calculation_id);
        if (!$payslip) {
            return '<p>Payslip not found.</p>';
        }

        $employee = $payslip['employee'];
        $calculation = $payslip['calculation'];
        $items = $payslip['items'];

        $html = '<div class="payslip">';
        $html .= '<h2>فیش حقوقی</h2>';
        $html .= '<p><strong>نام:</strong> ' . esc_html($employee['first_name'] . ' ' . $employee['last_name']) . '</p>';
        $html .= '<p><strong>کد کارمندی:</strong> ' . esc_html($employee['employee_code']) . '</p>';
        $html .= '<p><strong>ماه:</strong> ' . esc_html($calculation['calculation_month']) . '</p>';
        $html .= '<table border="1" cellpadding="10">';
        $html .= '<tr><th>شرح</th><th>مبلغ</th></tr>';

        foreach ($items as $item) {
            $html .= '<tr>';
            $html .= '<td>' . esc_html($item['name']) . '</td>';
            $html .= '<td>' . number_format((float) $item['amount'], 0, '.', ',') . '</td>';
            $html .= '</tr>';
        }

        $html .= '<tr><th>جمع درآمد</th><th>' . number_format((float) $calculation['total_gross'], 0, '.', ',') . '</th></tr>';
        $html .= '<tr><th>کسورات</th><th>' . number_format((float) $calculation['total_deductions'], 0, '.', ',') . '</th></tr>';
        $html .= '<tr><th>خالص پرداختی</th><th>' . number_format((float) $calculation['net_pay'], 0, '.', ',') . '</th></tr>';
        $html .= '</table>';
        $html .= '</div>';

        return $html;
    }
}
