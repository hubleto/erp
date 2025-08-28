<?php

namespace Hubleto\App\Community\Invoices;

class PriceCalculator extends \Hubleto\Framework\Core
{

  /**
   * [Description for calculateFullPrice]
   *
   * @param float $unitPrice
   * @param float $amount
   * 
   * @return float
   * 
   */
  public function calculateFullPrice(float $unitPrice, float $amount): float
  {
    return $unitPrice * $amount;
  }

  /**
   * [Description for calculateVat]
   *
   * @param float $fullPrice
   * @param float $vat
   * 
   * @return float
   * 
   */
  public function calculateVat(float $fullPrice, float $vat): float
  {
    return $fullPrice * $vat / 100;
  }

  /**
   * [Description for calculateDiscountedPrice]
   *
   * @param float $fullPrice
   * @param float $discount
   * 
   * @return float
   * 
   */
  public function calculateDiscountedPrice(float $fullPrice, float $discount): float
  {
    return $fullPrice * (1 - $discount / 100);
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
  public function calculatePriceExcludingVat(float $unitPrice, float $amount, float $discount = 0): float
  {
    $fullPrice = $this->calculateFullPrice($unitPrice, $amount);
    $finalPrice = $this->calculateDiscountedPrice($fullPrice, $discount);
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
  public function calculatePriceIncludingVat(float $unitPrice, float $amount, float $vat = 0, float $discount = 0): float
  {
    $priceExclVat = $this->calculatePriceExcludingVat($unitPrice, $amount, $discount);
    $finalPrice = $priceExclVat + $this->calculateVat($priceExclVat, $vat);
    return $finalPrice;
  }


}
