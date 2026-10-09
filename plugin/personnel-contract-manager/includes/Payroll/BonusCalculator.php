<?php
namespace PCM\Payroll;

if (!defined('ABSPATH')) {
    exit;
}

final class BonusCalculator
{
    public static function calculate_annual_bonus(float $monthly_salary, string $regulation_year): float
    {
        $regulation = SalaryRegulation::get_active_regulation($regulation_year);
        if (!$regulation) {
            return 0.0;
        }

        $data = json_decode((string) $regulation['data'], true);
        if (!is_array($data)) {
            return 0.0;
        }

        $bonus_multiplier = (float) ($data['annual_bonus_multiplier'] ?? 1.0);
        return $monthly_salary * $bonus_multiplier;
    }
}
