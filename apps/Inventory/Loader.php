<?php

namespace HubletoApp\Community\Inventory;

class Loader extends \Hubleto\Framework\App
{
  // init
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^inventory\/?$/' => Controllers\Inventory::class,
      '/^inventory\/transactions\/?$/' => Controllers\Transactions::class,
    ]);

    $appMenu = $this->main->apps->community('Desktop')->appMenu;
    $appMenu->addItem($this, 'inventory', $this->translate('Warehouses'), 'fas fa-boxes-stacked');
    $appMenu->addItem($this, 'inventory/transactions', $this->translate('Transactions'), 'fas fa-arrows-turn-to-dots');

  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\Status($this->main))->dropTableIfExists()->install();
      (new Models\Inventory($this->main))->dropTableIfExists()->install();
      (new Models\Transaction($this->main))->dropTableIfExists()->install();
    }
    if ($round == 2) {
      $mStatus = $this->main->di->create(Models\Status::class);
      $mStatus->record->recordCreate(['name' => 'Available']);
      $mStatus->record->recordCreate(['name' => 'Quarantined']);
      $mStatus->record->recordCreate(['name' => 'Damaged']);
      $mStatus->record->recordCreate(['name' => 'Reserved']);
    }
  }

  // generateDemoData
  public function generateDemoData(): void
  {
    // InventoryID,ProductID,LocationID,Quantity,BatchNumber,SerialNumber,ExpirationDate,ReceivedDate,LastMovedDate,Status
    // 1,1001,104,25,,,2023-01-05 15:00:00,2024-06-24 13:26:00,Available
    // 2,1002,103,10,,,2023-01-05 15:30:00,2024-06-24 13:26:00,Available
    // 3,1003,102,5,,SN123456789,,2023-01-06 09:30:00,2024-06-24 13:26:00,Available
    // 4,1001,201,50,,,2023-06-20 10:00:00,2023-06-20 10:00:00,Available
    // 5,1002,201,20,,,2023-06-20 10:15:00,2023-06-20 10:15:00,Available
    // 6,1005,103,40,,,2023-02-10 12:00:00,2024-06-24 13:26:00,Available
    // 7,1006,301,3,,,2023-03-01 14:00:00,2024-06-24 13:26:00,Available
    // 8,1007,104,100,BATT-001,,2025-12-31,2023-04-05 10:00:00,2024-06-24 13:26:00,Available
    // 9,1007,104,50,BATT-002,,2026-06-30,2024-05-10 11:00:00,2024-05-10 11:00:00,Available
    // 10,1003,105,1,,SN987654321,,2023-01-06 09:30:00,2024-06-24 13:26:00,Damaged
    // 11,1008,401,15,,SSD-001-A,,2024-01-20 11:00:00,2024-06-24 13:26:00,Available
    // 12,1008,401,5,,SSD-001-B,,2024-01-20 11:05:00,2024-06-24 13:26:00,Available

    // TransactionID,TransactionType,TransactionDate,ProductID,Quantity,SourceLocationID,DestinationLocationID,UserID,ReferenceDocumentID,Notes
    // 1,Receipt,2023-01-05 15:00:00,1001,25,,104,1,PO-2023-001,Initial stock for Wireless Mouse
    // 2,Receipt,2023-01-05 15:30:00,1002,10,,103,1,PO-2023-001,Initial stock for USB Keyboard
    // 3,Shipment,2024-06-24 10:00:00,1001,-5,104,,2,SO-2023-005,Customer order fulfillment
    // 4,Transfer In,2024-06-24 11:00:00,1001,10,201,104,3,TRF-2023-010,Moving stock from receiving to bin
    // 5,Adjustment In,2024-06-24 12:00:00,1004,2,,103,1,ADJ-2023-001,Discovered missing item during cycle count
    // 6,Receipt,2023-02-10 12:00:00,1005,40,,103,PO-2023-002,New shipment of HDMI cables
    // 7,Shipment,2024-06-24 13:00:00,1002,-3,103,,2,SO-2023-006,Partial order fulfillment
    // 8,Transfer Out,2024-06-24 14:00:00,1003,-1,102,105,3,TRF-2023-011,Monitor moved to quarantine (damaged)
    // 9,Receipt,2024-01-20 11:00:00,1008,20,,401,4,PO-2024-001,New SSDs received at West Coast Fulfillment
    // 10,Adjustment Out,2024-06-24 15:00:00,1007,-1,104,,1,ADJ-2023-002,Expired battery removed from stock
    // 11,Return,2024-06-24 16:00:00,1004,1,,501,1,RET-2024-001,Customer return of a laptop bag
  }

}
