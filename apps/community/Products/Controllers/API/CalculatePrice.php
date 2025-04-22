<?php

namespace HubletoApp\Community\Products\Controllers\API;

class CalculatePrice {

  // SINGLE PRODUCT FUNCTIONS
  public function calculatePriceWithAmount(float $unitPrice, float $amount): float {
    return $unitPrice * $amount;
  }

  public function calculateTax(float $sumPrice, float $tax): float {
    return $sumPrice * (1 + $tax / 100);
  }

  public function calculateDiscount(float $sumPrice, float $discount): float {
    return $sumPrice * (1 - $discount / 100);
  }

  public function calculatePriceBeforeTax(float $unitPrice, float $amount, float $discount = 0): float {
    $sumPrice = $this->calculatePriceWithAmount($unitPrice, $amount);
    if ($discount > 0) $sumPrice = $this->calculateDiscount($sumPrice, $discount);

    return $sumPrice;
  }

  public function calculatePriceAfterTax(float $unitPrice, float $amount, float $tax = 0, float $discount = 0): float {
    $sumPrice = $this->calculatePriceWithAmount($unitPrice, $amount);
    if ($discount > 0) $sumPrice = $this->calculateDiscount($sumPrice, $discount);
    if ($tax > 0) $sumPrice = $this->calculateTax($sumPrice, $tax);

    return $sumPrice;
  }

  //MULTI-PRODUCT FUNCTIONS
  public function calculateFinalPrice(array $productPrices): float {

    $finalPrice = 0;

    foreach ($productPrices as $price) {
      $finalPrice += $price;
    }

    return $finalPrice;
  }


}