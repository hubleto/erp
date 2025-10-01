<?php

namespace Hubleto\App\Community\Products;

class Loader extends \Hubleto\Framework\App
{

  public array $productTypes = [];

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^products\/?$/' => Controllers\Products::class,
      '/^products\/add?\/?$/' => ['controller' => Controllers\Products::class, 'vars' => [ 'recordId' => -1 ]],
      '/^products(\/(?<recordId>\d+))?\/?$/' => Controllers\Products::class,
      '/^products\/groups(\/(?<recordId>\d+))?\/?$/' => Controllers\Groups::class,
      '/^products\/groups\/add?\/?$/' => ['controller' => Controllers\Groups::class, 'vars' => [ 'recordId' => -1 ]],
    ]);

    $appMenu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    $appMenu->addItem($this, 'products', $this->translate('Products'), 'fas fa-cart-shopping');
    $appMenu->addItem($this, 'products/groups', $this->translate('Groups'), 'fas fa-burger');

    $this->productTypes = $this->collectExtendibles('ProductTypes');
  }

  /**
   * [Description for installTables]
   *
   * @param int $round
   * 
   * @return void
   * 
   */
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Group::class)->dropTableIfExists()->install();
      $this->getModel(Models\Product::class)->dropTableIfExists()->install();
      $this->getModel(Models\ProductSupplier::class)->dropTableIfExists()->install();
    }
  }


  /**
   * Implements fulltext search functionality for the contacts
   *
   * @param array $expressions List of expressions to be searched and glued with logical 'or'.
   * 
   * @return array
   * 
   */
  public function search(array $expressions): array
  {
    $mProduct = $this->getModel(Models\Product::class);
    $qProducts = $mProduct->record->prepareReadQuery();
    
    foreach ($expressions as $e) {
      $qProducts = $qProducts->orWhere('products.ean', $e);
      $qProducts = $qProducts->orWhere('products.name', 'like', '%' . $e . '%');
    }

    $products = $qProducts->get()->toArray();

    $results = [];

    foreach ($products as $product) {
      $results[] = [
        "id" => $product['id'],
        "label" => $product['ean'] . ' ' . $product['name'],
        "url" => 'products/' . $product['id'],
        "description" => $product['GROUP']['title'],
      ];
    }

    return $results;
  }
}
