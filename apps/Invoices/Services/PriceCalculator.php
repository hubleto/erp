<?php

namespace Hubleto\App\Community\Invoices\Services;

use Hubleto\Erp\Interfaces\PriceCalculatorInterface;

class PriceCalculator extends \Hubleto\Erp\Core implements PriceCalculatorInterface
{

  public const ROUNDING_POLICY_NONE = 0;
  public const ROUNDING_POLICY_NEAREST = 1;
  public const ROUNDING_POLICY_UP = 2;
  public const ROUNDING_POLICY_DOWN = 3;
  public const ROUNDING_POLICY_ENDS_IN = 4;

  public function calculateFullPrice(float $unitPrice, float $amount): float
  {
    return $unitPrice * $amount;
  }

  public function calculateVat(float $fullPrice, float $vatPercent): float
  {
    return $fullPrice * $vatPercent / 100;
  }

  public function calculateDiscountedPrice(float $fullPrice, float $discountPercent): float
  {
    return $fullPrice * (1 - $discountPercent / 100);
  }

  /**
   * [Description for calculatePriceExcludingVat]
   *
   * @param float $unitPrice
   * @param float $amount
   * @param float $discountPercent
   * 
   * @return float
   * 
   */
  public function calculatePriceExcludingVat(float $unitPrice, float $amount, float $discountPercent = 0): float
  {
    $fullPrice = $this->calculateFullPrice($unitPrice, $amount);
    $finalPrice = $this->calculateDiscountedPrice($fullPrice, $discountPercent);
    return $finalPrice;
  }

  /**
   * [Description for calculatePriceIncludingVat]
   *
   * @param float $unitPrice
   * @param float $amount
   * @param float $vatPercent
   * @param float $discountPercent
   * 
   * @return float
   * 
   */
  public function calculatePriceIncludingVat(float $unitPrice, float $amount, float $discountPercent = 0, float $vatPercent = 0): float
  {
    $priceExclVat = $this->calculatePriceExcludingVat($unitPrice, $amount, $discountPercent);
    $finalPrice = $priceExclVat + $this->calculateVat($priceExclVat, $vatPercent);
    return $finalPrice;
  }


  /**
   * [Description for calculateItem]
   *
   * @param array $item
   * @param array $options
   * 
   * @return array
   * 
   */
  public function calculateItem(array $item, array $options = []): array
  {
    return [
      'price_excl_vat' => $item['price_excl_vat'] ?? 0,
      'price_vat' => $item['price_vat'] ?? 0,
      'price_incl_vat' => $item['price_incl_vat'] ?? 0,
    ];
  }

  /**
   * [Description for calculateItems]
   *
   * @param array $items
   * @param array $options
   * 
   * @return array
   * 
   */
  public function calculateItems(array $items, array $options = []): array
  {
    $itemsCalculated = [];
    foreach ($items as $item) {
      $itemsCalculated[] = $this->calculateItem($item, $options);
    }
    return $itemsCalculated;
  }

  /**
   * [Description for calculateTotals]
   *
   * @param array $items
   * @param array $options
   * 
   * @return array
   * 
   */
  public function calculateTotals(array $items, array $options = []): array
  {
    $totalExclVat = 0;
    $totalInclVat = 0;

    foreach ($items as $item) {
      $itemCalculated = $this->calculateItem($item);
      $totalExclVat += $itemCalculated['price_excl_vat'];
      $totalInclVat += $itemCalculated['price_incl_vat'];
    }

    return [
      'total_excl_vat' => $totalExclVat,
      'total_incl_vat' => $totalInclVat,
    ];
  }

  /**
   * [Description for isPaidInFull]
   *
   * @param float $total
   * @param float $paid
   * @param int $decimals
   * 
   * @return bool
   * 
   */
  public function isPaidInFull(float $total, float $paid, int $decimals = 2): bool
  {
    return $paid >= $total;
  }

  /**
   * [Description for sqlItemExclVat]
   *
   * @param string $table
   * @param array $columns
   * 
   * @return string
   * 
   */
  public function sqlItemExclVat(string $table, array $columns = []): string
  {
    return '';
  }

  /**
   * [Description for sqlItemVat]
   *
   * @param string $table
   * @param array $columns
   * 
   * @return string
   * 
   */
  public function sqlItemVat(string $table, array $columns = []): string
  {
    return '';
  }

  /**
   * [Description for sqlItemInclVat]
   *
   * @param string $table
   * @param array $columns
   * 
   * @return string
   * 
   */
  public function sqlItemInclVat(string $table, array $columns = []): string
  {
    return '';
  }

  /**
   * Rounds the value according to the policy.
   *
   * @param float $value
   * @param array|null $policy
   *
   * @return float
   *
   */
  public function round(float $value, ?array $policy = null): float
  {
    $policy = array_merge(['policy' => self::ROUNDING_POLICY_NONE, 'step' => 0.0, 'ending' => 0.0, 'decimals' => null], $policy ?? []);
    $step = (float) $policy['step'];
    $decimals = $policy['decimals'] === null ? null : (int) $policy['decimals'];

    switch ((int) $policy['policy']) {
      case self::ROUNDING_POLICY_NEAREST:
        $result = $step > 0 ? $this->roundToStep($value, $step, PHP_ROUND_HALF_UP) : round($value, $decimals ?? 2, PHP_ROUND_HALF_UP);
      break;
      case self::ROUNDING_POLICY_UP:
        $result = $step > 0 ? $this->ceilToStep($value, $step) : ceil(round($value, 8));
      break;
      case self::ROUNDING_POLICY_DOWN:
        $result = $step > 0 ? $this->floorToStep($value, $step) : floor(round($value, 8));
      break;
      case self::ROUNDING_POLICY_ENDS_IN:
        $result = $this->roundEndsIn($value, (float) $policy['ending']);
      break;
      default:
        $result = $value;
    }

    return round($result, $decimals ?? 8, PHP_ROUND_HALF_UP);
  }

  /** Rounds a money value to the given number of decimals (half up). */
  public function roundMoney(float $value, int $decimals): float
  {
    return round($value, $decimals, PHP_ROUND_HALF_UP);
  }

  /** Rounds to the nearest multiple of the step. */
  public function roundToStep(float $value, float $step, int $mode = PHP_ROUND_HALF_UP): float
  {
    if ($step <= 0) return $value;
    return round(round($value / $step, 8), 0, $mode) * $step;
  }

  /** Rounds up to a multiple of the step. */
  public function ceilToStep(float $value, float $step): float
  {
    if ($step <= 0) return $value;
    return ceil(round($value / $step, 8)) * $step;
  }

  /** Rounds down to a multiple of the step. */
  public function floorToStep(float $value, float $step): float
  {
    if ($step <= 0) return $value;
    return floor(round($value / $step, 8)) * $step;
  }

  /**
   * Rounds to the nearest price that ends with the given fraction, e.g. 0.99 (12.47 becomes 12.99, 12.10 becomes 11.99).
   */
  public function roundEndsIn(float $value, float $ending): float
  {
    $base = floor($value);
    $upper = $base + $ending;
    $lower = $base - 1 + $ending;

    return (abs($upper - $value) <= abs($value - $lower)) ? $upper : $lower;
  }

  /** Cash rounding of a payable amount to a step (0 = off). */
  public function cashRound(float $value, float $step): float
  {
    if ($step <= 0) return $value;
    return $this->roundToStep($value, $step);
  }

}
