<?php

namespace Hubleto\App\Community\Suppliers;

class Loader extends \Hubleto\Framework\App
{

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
      '/^suppliers\/?$/' => Controllers\Suppliers::class,
    ]);
  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Supplier::class)->dropTableIfExists()->install();
    }
  }

  // generateDemoData
  public function generateDemoData(): void
  {
    // SupplierID,SupplierName,ContactPerson,Phone,Email,Address
    // 10,ElectroCorp,Sarah Lee,555-777-8888,sarah@electrocorp.com,"789 Tech Drive, Silicon Valley, CA"
    // 11,OfficeSupplyPro,David Chen,555-999-0000,david@officesupplypro.com,"100 Office Blvd, Business City, NY"
    // 12,TravelGear Inc.,Emily White,555-123-4567,emily@travelgear.com,"200 Adventure Ave, Outdoor Town, OR"
    // 13,ComfortSeats Global,Mark Davis,555-789-0123,mark@comfortseats.com,"300 Chair Lane, Furniture City, NC"
    // 14,PowerUp Solutions,Laura Kim,555-321-7654,laura@powerupsolutions.com,"400 Battery Rd, Energy Town, PA"
    $mSupplier = $this->getModel(Models\Supplier::class);
    $mSupplier->record->recordCreate(['name' => 'ElectroCorp']);
  }

}
