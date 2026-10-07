<?php

namespace Hubleto\App\Community\Products\Services;

use Hubleto\Erp\Interfaces\PriceCalculatorInterface;

class PriceCalculator extends \Hubleto\Erp\Core implements PriceCalculatorInterface
{
  // SINGLE PRODUCT FUNCTIONS
  public function calculateFullPrice(float $unitPrice, float $amount): float
  {
    return $unitPrice * $amount;
  }

  public function calculateVat(float $fullPrice, float $vat): float
  {
    return $fullPrice * $vat / 100;
  }

  public function calculateDiscountedPrice(float $fullPrice, float $discount): float
  {
    return $fullPrice * (1 - $discount / 100);
  }

  public function calculatePriceExcludingVat(float $unitPrice, float $amount, float $discountPercent = 0): float
  {
    $fullPrice = $this->calculateFullPrice($unitPrice, $amount);
    $finalPrice = $this->calculateDiscountedPrice($fullPrice, $discountPercent);

    return $finalPrice;
  }

  public function calculatePriceIncludingVat(float $unitPrice, float $amount, float $discountPercent = 0, float $vatPercent = 0): float
  {
    $priceExclVat = $this->calculatePriceExcludingVat($unitPrice, $amount, $discountPercent);
    $finalPrice = $priceExclVat + $this->calculateVat($priceExclVat, $vatPercent);

    return $finalPrice;
  }

  //MULTI-PRODUCT FUNCTIONS
  public function calculateFinalPrice(array $productPrices): float
  {

    $finalPrice = 0;

    foreach ($productPrices as $price) {
      $finalPrice += $price;
    }

    return $finalPrice;
  }


}
