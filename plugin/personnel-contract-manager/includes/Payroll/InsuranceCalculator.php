<?php
namespace PCM\Payroll;

if (!defined('ABSPATH')) {
    exit;
}

final class InsuranceCalculator
{
    public static function calculate_employee_insurance(float $gross, string $regulation_year): array
    {
        $regulation = SalaryRegulation::get_active_regulation($regulation_year);
        if (!$regulation) {
            return [
                'employee_share' => 0.0,
                'employer_share' => 0.0,
            ];
        }

        $data = json_decode((string) $regulation['data'], true);
        if (!is_array($data)) {
            return [
                'employee_share' => 0.0,
                'employer_share' => 0.0,
            ];
        }

        $employee_rate = (float) ($data['insurance_employee_rate'] ?? 10.5);
        $employer_rate = (float) ($data['insurance_employer_rate'] ?? 28.5);

        return [
            'employee_share' => $gross * ($employee_rate / 100),
            'employer_share' => $gross * ($employer_rate / 100),
        ];
    }
}
