<?php

namespace HubletoApp\Community\Shop\Controllers\Api;

use HubletoApp\Community\Shop\Models\Product;
use Exception;

class GetProductPrice extends \HubletoMain\Core\Controller {

  public function renderJson(): ?array
  {
    $mProduct = new Product($this->main);
    $product = null;

    try {
      $product = $mProduct->eloquent
        ->find($this->main->params["productId"])
        ->toArray()
      ;
    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "unit_price" => $product["price"],
      "status" => "success"
    ];
  }

}
