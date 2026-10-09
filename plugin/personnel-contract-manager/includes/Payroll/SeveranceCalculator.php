<?php
namespace PCM\Payroll;

if (!defined('ABSPATH')) {
    exit;
}

final class SeveranceCalculator
{
    public static function calculate_severance(float $monthly_salary, int $service_months, string $regulation_year): float
    {
        $regulation = SalaryRegulation::get_active_regulation($regulation_year);
        if (!$regulation) {
            return 0.0;
        }

        $data = json_decode((string) $regulation['data'], true);
        if (!is_array($data)) {
            return 0.0;
        }

        $severance_rate = (float) ($data['severance_rate'] ?? 0);
        $min_service_months = (int) ($data['min_service_months'] ?? 12);

        if ($service_months < $min_service_months) {
            return 0.0;
        }

        return $monthly_salary * $severance_rate * ($service_months / 12);
    }
}
