<?php declare(strict_types=1);

namespace Hubleto\Erp\Interfaces;

interface PriceCalculatorInterface
{

  /** Calculate full price based on unit price and amount. */
  public function calculateFullPrice(float $unitPrice, float $amount): float;

  /** Calculate VAT based on full price and VAT percent. */
  public function calculateVat(float $fullPrice, float $vatPercent): float;

  /** Calculate discount based on full price and discount percent. */
  public function calculateDiscountedPrice(float $fullPrice, float $discountPercent): float;

  /** Calculate price excluding VAT based on unit price, amount and optionally discount percent. */
  public function calculatePriceExcludingVat(float $unitPrice, float $amount, float $discountPercent = 0): float;

  /** Calculate price including VAT based on unit price, amount and optionally discount percent and VAT percent. */
  public function calculatePriceIncludingVat(float $unitPrice, float $amount, float $discountPercent = 0, float $vatPercent = 0): float;

  public function calculateItem(array $item, array $options = []): array;
  public function calculateItems(array $items, array $options = []): array;
  public function calculateTotals(array $items, array $options = []): array;
  public function isPaidInFull(float $total, float $paid, int $decimals = 2): bool;
  public function sqlItemExclVat(string $table, array $columns = []): string;
  public function sqlItemVat(string $table, array $columns = []): string;
  public function sqlItemInclVat(string $table, array $columns = []): string;


  public function round(float $value, ?array $policy = null): float;
  public function roundMoney(float $value, int $decimals): float;
  public function roundToStep(float $value, float $step, int $mode = PHP_ROUND_HALF_UP): float;
  public function ceilToStep(float $value, float $step): float;
  public function floorToStep(float $value, float $step): float;
  public function roundEndsIn(float $value, float $ending): float;
  public function cashRound(float $value, float $step): float;

}
