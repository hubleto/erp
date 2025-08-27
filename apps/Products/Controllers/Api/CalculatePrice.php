<?php

namespace HubletoApp\Community\Products\Controllers\Api;

class CalculatePrice extends \HubletoMain\Controllers\ApiController
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

  public function calculatePriceExcludingVat(float $unitPrice, float $amount, float $discount = 0): float
  {
    $fullPrice = $this->calculateFullPrice($unitPrice, $amount);
    $finalPrice = $this->calculateDiscountedPrice($fullPrice, $discount);

    return $finalPrice;
  }

  public function calculatePriceIncludingVat(float $unitPrice, float $amount, float $vat = 0, float $discount = 0): float
  {
    $priceExclVat = $this->calculatePriceExcludingVat($unitPrice, $amount, $discount);
    $finalPrice = $priceExclVat + $this->calculateVat($priceExclVat, $vat);

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
