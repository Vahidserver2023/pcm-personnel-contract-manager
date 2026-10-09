<?php
namespace PCM\Payroll;

if (!defined('ABSPATH')) {
    exit;
}

final class TaxCalculator
{
    public static function calculate_tax(float $gross, string $regulation_year): float
    {
        $regulation = SalaryRegulation::get_active_regulation($regulation_year);
        if (!$regulation) {
            return 0.0;
        }

        $data = json_decode((string) $regulation['data'], true);
        if (!is_array($data)) {
            return 0.0;
        }

        $tax_rate = (float) ($data['tax_rate'] ?? 10);
        $tax_exemption = (float) ($data['tax_exemption'] ?? 0);

        if ($gross <= $tax_exemption) {
            return 0.0;
        }

        $taxable_amount = $gross - $tax_exemption;
        return $taxable_amount * ($tax_rate / 100);
    }
}
