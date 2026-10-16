<?php

namespace Hubleto\App\Community\PriceLists\Services;

use Hubleto\Erp\Interfaces\PriceCalculatorInterface;

/**
 * All price arithmetic of the PriceList app (and, once activated in their config, of Orders, Deals and Invoices).
 *
 * Implements the contract used by community apps (PriceCalculatorInterface) with results identical to
 * Products\Services\PriceCalculator, and adds item, totals, VAT-mode, margin, currency and SQL helpers.
 *
 * Item-level arrays use the column names of the community item models:
 *   input:  unit_price, amount, discount, vat, price_includes_vat
 *   output: full_price, discount_amount, unit_price_excl_vat, unit_vat, unit_price_incl_vat,
 *           price_excl_vat, price_vat, price_incl_vat
 */
class PriceCalculator extends \Hubleto\Erp\Core implements PriceCalculatorInterface
{
  public const ROUNDING_POLICY_NONE = 0;
  public const ROUNDING_POLICY_NEAREST = 1;
  public const ROUNDING_POLICY_UP = 2;
  public const ROUNDING_POLICY_DOWN = 3;
  public const ROUNDING_POLICY_ENDS_IN = 4;

  // ---------------------------------------------------------------------------------------------------------------
  // Contract of PriceCalculatorInterface (unrounded)
  // ---------------------------------------------------------------------------------------------------------------

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

  public function calculatePriceExcludingVat(float $unitPrice, float $amount, float $discountPercent = 0): float
  {
    $fullPrice = $this->calculateFullPrice($unitPrice, $amount);
    return $this->calculateDiscountedPrice($fullPrice, $discountPercent);
  }

  public function calculatePriceIncludingVat(float $unitPrice, float $amount, float $discountPercent = 0, float $vatPercent = 0): float
  {
    $priceExclVat = $this->calculatePriceExcludingVat($unitPrice, $amount, $discountPercent);
    return $priceExclVat + $this->calculateVat($priceExclVat, $vatPercent);
  }

  /** Sum of prices. Not part of the interface; kept for callers of the former Products service. */
  public function calculateFinalPrice(array $productPrices): float
  {
    $finalPrice = 0;
    foreach ($productPrices as $price) $finalPrice += (float) $price;
    return $finalPrice;
  }

  // ---------------------------------------------------------------------------------------------------------------
  // Items and totals
  // ---------------------------------------------------------------------------------------------------------------

  /**
   * Calculates all price values of one document item.
   *
   * Options: vat_basis ('item' = VAT computed on the item net price, 'unit' = VAT computed per unit and multiplied),
   *          rounding ('none', 'unit', 'item'), decimals_unit, decimals_item.
   *
   * @param array $item
   * @param array $options
   *
   * @return array
   *
   */
  public function calculateItem(array $item, array $options = []): array
  {
    $options = array_merge($this->defaultOptions(), $options);

    $unitPrice = (float) ($item['unit_price'] ?? 0);
    $amount = (float) ($item['amount'] ?? 0);
    $discount = (float) ($item['discount'] ?? 0);
    $vat = (float) ($item['vat'] ?? 0);

    if (!empty($item['price_includes_vat'])) {
      $unitPrice = $this->convertVatMode($unitPrice, $vat, true, false);
    }

    $fullPrice = $this->calculateFullPrice($unitPrice, $amount);
    $unitNet = $this->calculateDiscountedPrice($unitPrice, $discount);
    $unitVat = $this->calculateVat($unitNet, $vat);

    $rounding = (string) $options['rounding'];
    $decimalsUnit = (int) $options['decimals_unit'];
    $decimalsItem = (int) $options['decimals_item'];

    if ($rounding === 'unit') {
      $unitNet = $this->roundMoney($unitNet, $decimalsUnit);
      $unitVat = $this->roundMoney($unitVat, $decimalsUnit);
    }

    $unitIncl = $unitNet + $unitVat;

    if ($rounding === 'unit' || $options['vat_basis'] === 'unit') {
      $priceExcl = $unitNet * $amount;
      $priceVat = $unitVat * $amount;
    } else {
      $priceExcl = $this->calculatePriceExcludingVat($unitPrice, $amount, $discount);
      $priceVat = $this->calculateVat($priceExcl, $vat);
    }

    if ($rounding === 'item' || $rounding === 'unit') {
      $priceExcl = $this->roundMoney($priceExcl, $decimalsItem);
      $priceVat = $this->roundMoney($priceVat, $decimalsItem);
    }

    return [
      'full_price' => $fullPrice,
      'discount_amount' => $fullPrice - ($unitNet * $amount),
      'unit_price_excl_vat' => $unitNet,
      'unit_vat' => $unitVat,
      'unit_price_incl_vat' => $unitIncl,
      'price_excl_vat' => $priceExcl,
      'price_vat' => $priceVat,
      'price_incl_vat' => $priceExcl + $priceVat,
    ];
  }

  /**
   * Calculates many items and their totals.
   *
   * @param array $items
   * @param array $options
   *
   * @return array ['items' => [...], 'totals' => [...]]
   *
   */
  public function calculateItems(array $items, array $options = []): array
  {
    $calculated = [];
    foreach ($items as $key => $item) {
      $calculated[$key] = array_merge($item, $this->calculateItem($item, $options));
    }
    return ['items' => $calculated, 'totals' => $this->calculateTotals($calculated, $options)];
  }

  /**
   * Calculates totals of a document.
   *
   * Items may be raw (unit_price, amount, discount, vat) or already calculated (price_excl_vat, price_incl_vat).
   * Option vat_mode: 'per_item' sums VAT of each item; 'per_rate_group' computes VAT on the sum of bases of each rate.
   *
   * @param array $items
   * @param array $options
   *
   * @return array
   *
   */
  public function calculateTotals(array $items, array $options = []): array
  {
    $options = array_merge($this->defaultOptions(), $options);

    $decimalsItem = (int) $options['decimals_item'];

    $groups = [];
    $totalExcl = 0.0;
    $totalVat = 0.0;

    foreach ($items as $item) {
      if (!empty($item['_toBeDeleted_'])) continue;

      if (!isset($item['price_excl_vat'])) {
        $item = array_merge($item, $this->calculateItem($item, $options));
      }

      $rate = (float) ($item['vat'] ?? 0);
      $key = (string) $rate;
      if (!isset($groups[$key])) $groups[$key] = ['rate' => $rate, 'base' => 0.0, 'vat' => 0.0];

      $itemExcl = (float) $item['price_excl_vat'];
      $itemVat = isset($item['price_vat']) ? (float) $item['price_vat'] : (float) $item['price_incl_vat'] - $itemExcl;

      $groups[$key]['base'] += $itemExcl;
      $groups[$key]['vat'] += $itemVat;
      $totalExcl += $itemExcl;
      $totalVat += $itemVat;
    }

    if ($options['vat_mode'] === 'per_rate_group') {
      $totalVat = 0.0;
      foreach ($groups as $key => $group) {
        $groupVat = $this->calculateVat($group['base'], $group['rate']);
        if ($options['rounding'] !== 'none') $groupVat = $this->roundMoney($groupVat, $decimalsItem);
        $groups[$key]['vat'] = $groupVat;
        $totalVat += $groupVat;
      }
    }

    $totalIncl = $totalExcl + $totalVat;
    $payable = $this->cashRound($totalIncl, (float) $options['cash_rounding']);

    $summary = [];
    foreach ($groups as $group) {
      $summary[] = [
        'rate' => $group['rate'],
        'base' => $group['base'],
        'vat' => $group['vat'],
        'gross' => $group['base'] + $group['vat'],
      ];
    }

    return [
      'total_excl_vat' => $totalExcl,
      'total_vat' => $totalVat,
      'total_incl_vat' => $totalIncl,
      'vat_summary' => $summary,
      'rounding_adjustment' => $payable - $totalIncl,
      'total_payable' => $payable,
    ];
  }

  // ---------------------------------------------------------------------------------------------------------------
  // VAT mode, discounts, margins, currency, comparison
  // ---------------------------------------------------------------------------------------------------------------

  /** Converts a price between "with VAT" and "without VAT". */
  public function convertVatMode(float $price, float $vat, bool $fromInclVat, bool $toInclVat): float
  {
    if ($fromInclVat === $toInclVat) return $price;
    if ($fromInclVat) return $price / (1 + $vat / 100);
    return $price * (1 + $vat / 100);
  }

  /** Discount in percent between a list price and an actual price. */
  public function calculateDiscountPercent(float $listPrice, float $price): float
  {
    if ($listPrice <= 0) return 0.0;
    return ($listPrice - $price) / $listPrice * 100;
  }

  /** Combines several percentage discounts into one (multiplicatively). */
  public function combineDiscountPercents(array $percents): float
  {
    $factor = 1.0;
    foreach ($percents as $percent) $factor *= (1 - (float) $percent / 100);
    return (1 - $factor) * 100;
  }

  public function addAmount(float $price, float $amount): float
  {
    return $price + $amount;
  }

  public function subtractAmount(float $price, float $amount): float
  {
    return $price - $amount;
  }

  /** Increases a price by a percentage. */
  public function applyPercentOn(float $price, float $percent): float
  {
    return $price * (1 + $percent / 100);
  }

  public function priceFromCostMarkup(float $cost, float $markupPercent): float
  {
    return $cost * (1 + $markupPercent / 100);
  }

  public function priceFromCostMargin(float $cost, float $marginPercent): float
  {
    if ($marginPercent >= 100) {
      throw new \InvalidArgumentException($this->translate('Margin must be lower than 100 %.'));
    }
    return $cost / (1 - $marginPercent / 100);
  }

  public function markupPercent(float $price, float $cost): float
  {
    if ($cost <= 0) return 0.0;
    return ($price - $cost) / $cost * 100;
  }

  public function marginPercent(float $price, float $cost): float
  {
    if ($price <= 0) return 0.0;
    return ($price - $cost) / $price * 100;
  }

  /** Multiplication by an exchange rate (rate = units of the target currency for 1 unit of the source). */
  public function convertCurrency(float $amount, float $rate): float
  {
    return $amount * $rate;
  }

  /** Is the total paid? Mirrors the check used for invoices (the caller adds its own "has payments" condition). */
  public function isPaidInFull(float $total, float $paid, int $decimals = 2): bool
  {
    return $total > 0 && round($paid, $decimals) >= round($total, $decimals);
  }

  // ---------------------------------------------------------------------------------------------------------------
  // SQL builders (expressions to be used inside sum(...) in record managers)
  // ---------------------------------------------------------------------------------------------------------------

  /**
   * SQL expression of the item price excluding VAT.
   *
   * @param string $table Table or alias, e.g. 'orders_items'
   * @param array $columns Column names if they differ from unit_price, amount, discount, vat
   *
   * @return string
   *
   */
  public function sqlItemExclVat(string $table, array $columns = []): string
  {
    $c = $this->sqlColumns($table, $columns);
    return "ifnull({$c['unit_price']}, 0) * ifnull({$c['amount']}, 0) * (1 - ifnull({$c['discount']}, 0)/100)";
  }

  /** SQL expression of the item VAT. */
  public function sqlItemVat(string $table, array $columns = []): string
  {
    $c = $this->sqlColumns($table, $columns);
    return $this->sqlItemExclVat($table, $columns) . " * ifnull({$c['vat']}, 0)/100";
  }

  /** SQL expression of the item price including VAT. */
  public function sqlItemInclVat(string $table, array $columns = []): string
  {
    $c = $this->sqlColumns($table, $columns);
    return $this->sqlItemExclVat($table, $columns) . " * (1 + ifnull({$c['vat']}, 0)/100)";
  }

  protected function sqlColumns(string $table, array $columns): array
  {
    $columns = array_merge(['unit_price' => 'unit_price', 'amount' => 'amount', 'discount' => 'discount', 'vat' => 'vat'], $columns);
    $result = [];
    foreach ($columns as $key => $column) $result[$key] = $table . '.' . $column;
    return $result;
  }

  // ---------------------------------------------------------------------------------------------------------------

  /** Options taken from settings, with safe defaults. */
  protected function defaultOptions(): array
  {
    $defaults = ['vat_basis' => 'item', 'rounding' => 'none', 'vat_mode' => 'per_item', 'decimals_unit' => 4, 'decimals_item' => 2, 'cash_rounding' => 0.0];

    try {
      /** @var Settings $settings */
      $settings = $this->getService(Settings::class);
      return array_merge($defaults, $settings->getCalculatorOptions());
    } catch (\Throwable $e) {
      return $defaults;
    }
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
