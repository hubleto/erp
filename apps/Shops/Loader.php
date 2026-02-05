<?php

namespace Hubleto\App\Community\Shops;

class Loader extends \Hubleto\Erp\App
{

  /**
   * Inits the app: adds routes, settings, calendars, event listeners, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^shops(\/(?<recordId>\d+))?\/?$/' => Controllers\Shops::class,
      '/^shops\/add?\/?$/' => ['controller' => Controllers\Shops::class, 'vars' => [ 'recordId' => -1 ]],
    ]);

  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Shop::class)->dropTableIfExists()->install();
    }
  }

  // generateDemoData
  public function generateDemoData(): void
  {
    $mShop = $this->getModel(Models\Shop::class);

    $mShop->record->recordCreate([
      'name' => 'New York, shop 1/3',
      'address' => '63 Durham Road, Ridgewood, NY 11385',
      'color' => '#345678',
    ]);

    $mShop->record->recordCreate([
      'name' => 'New York, shop 2/3',
      'address' => '880 Briarwood Drive, New York, NY 10023',
      'color' => '#34785cff',
    ]);

    $mShop->record->recordCreate([
      'name' => 'New York, shop 3/3',
      'address' => '40 Pin Oak Drive, Bronx, NY 10463',
      'color' => '#785534ff',
    ]);
  }

}
