<?php

namespace Hubleto\App\Community\Invoices\Services;

use Hubleto\Erp\Interfaces\PriceCalculatorInterface;

class PriceCalculator extends \Hubleto\Erp\Core implements PriceCalculatorInterface
{

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

}
