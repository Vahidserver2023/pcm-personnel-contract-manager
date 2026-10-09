<?php
namespace PCM\Payroll;

if (!defined('ABSPATH')) {
    exit;
}

final class SalaryCalculationEngine
{
    public static function calculate(array $employee, array $contract, array $regulationData, array $payItems): array
    {
        $baseSalary = (float) ($contract['monthly_salary'] ?? 0);
        $gross = $baseSalary;
        $deductions = 0.0;
        $items = [];

        foreach ($payItems as $payItem) {
            $amount = 0.0;
            $rate = (float) ($payItem['percentage'] ?? 0);
            $baseValue = (float) ($payItem['amount'] ?? 0);
            $quantity = 1.0;

            if (!empty($payItem['calculation_type']) && $payItem['calculation_type'] === 'percentage') {
                $amount = $baseSalary * ($rate / 100);
            } elseif (!empty($payItem['calculation_type']) && $payItem['calculation_type'] === 'fixed') {
                $amount = (float) ($payItem['fixed_amount'] ?? 0);
            } else {
                $amount = $baseValue;
            }

            $gross += $amount;

            $items[] = [
                'name' => $payItem['name'] ?? '',
                'base_value' => $baseSalary,
                'rate' => $rate,
                'quantity' => $quantity,
                'formula' => $payItem['formula'] ?? '',
                'amount' => $amount,
                'regulation_year' => $regulationData['regulation_year'] ?? date('Y'),
                'legal_basis' => $payItem['legal_basis'] ?? '',
            ];
        }

        $net = $gross - $deductions;

        return [
            'gross' => $gross,
            'deductions' => $deductions,
            'net' => $net,
            'items' => $items,
            'regulation' => $regulationData,
        ];
    }
}
