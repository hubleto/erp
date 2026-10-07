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
   * @param float $discount
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
   * @param float $vat
   * @param float $discount
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

}
