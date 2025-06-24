<?php

namespace HubletoApp\Community\Warehouses;

class Loader extends \HubletoMain\Core\App
{

  // init
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^warehouses\/?$/' => Controllers\Warehouses::class,
      '/^warehouses\/locations\/?$/' => Controllers\Locations::class,
    ]);

  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\Warehouse($this->main))->dropTableIfExists()->install();
      (new Models\Location($this->main))->dropTableIfExists()->install();
    }
  }

  // generateDemoData
  public function generateDemoData(): void
  {
    // WarehouseID,WarehouseName,AddressLine1,AddressLine2,City,StateProvince,PostalCode,Country,ContactPerson,ContactPhone,IsActive,CreatedAt,UpdatedAt
    // 1,Main Distribution Center,123 Warehouse St,,Anytown,CA,90210,USA,John Doe,555-111-2222,TRUE,2023-01-01 10:00:00,2023-01-01 10:00:00
    // 2,Regional Hub East,456 Industrial Rd,,Eastville,NY,10001,USA,Jane Smith,555-333-4444,TRUE,2023-02-15 11:30:00,2023-02-15 11:30:00
    // 3,Satellite Storage A,789 Storage Ln,,Smallville,TX,75001,USA,Robert Johnson,555-555-6666,TRUE,2023-03-20 09:00:00,2023-03-20 09:00:00
    // 4,West Coast Fulfillment,999 Port Ave,,Seaport,WA,98101,USA,Maria Garcia,555-666-7777,TRUE,2024-01-10 08:00:00,2024-01-10 08:00:00
    // 5,Central Returns Facility,500 Return Rd,,Center City,IL,60601,USA,Chris Lee,555-888-9999,TRUE,2024-02-01 13:00:00,2024-02-01 13:00:00

    // LocationID,WarehouseID,ParentLocationID,LocationCode,LocationType,Description,CapacityUnit,MaxCapacity,CurrentOccupancy,IsAvailable,CreatedAt,UpdatedAt
    // 101,1,,A1,Aisle,Main aisle in Warehouse 1,Cubic Feet,10000.00,2500.00,TRUE,2023-01-01 10:15:00,2024-06-24 13:26:00
    // 102,1,101,A1R1,Rack,Rack 1 in Aisle 1,Cubic Feet,500.00,120.00,TRUE,2023-01-01 10:20:00,2024-06-24 13:26:00
    // 103,1,102,A1R1S1,Shelf,Shelf 1 on Rack 1,Cubic Feet,50.00,10.00,TRUE,2023-01-01 10:25:00,2024-06-24 13:26:00
    // 104,1,103,A1R1S1B1,Bin,Bin 1 on Shelf 1,Pcs,100.00,25.00,TRUE,2023-01-01 10:30:00,2024-06-24 13:26:00
    // 105,1,,Q-ZONE,Quarantine Area,Area for damaged or returned goods,Pcs,500.00,0.00,TRUE,2023-04-01 10:00:00,2024-06-24 13:26:00
    // 201,2,,R-ZONE,Receiving Area,Dedicated area for inbound goods,Cubic Feet,2000.00,500.00,TRUE,2023-02-15 11:45:00,2024-06-24 13:26:00
    // 202,2,,S-ZONE,Shipping Area,Dedicated area for outbound goods,Cubic Feet,2000.00,300.00,TRUE,2023-02-15 11:50:00,2024-06-24 13:26:00
    // 203,2,,P-ZONE,Picking Zone,Main picking area for small items,Pcs,1500.00,700.00,TRUE,2023-03-01 09:00:00,2024-06-24 13:26:00
    // 301,3,,BULK-A,Bulk Storage A,Large item bulk storage,Cubic Feet,5000.00,1000.00,TRUE,2023-03-20 09:30:00,2024-06-24 13:26:00
    // 401,4,,S1A1,Shelf,Shelf 1 in Aisle 1,Pcs,200.00,50.00,TRUE,2024-01-10 08:30:00,2024-06-24 13:26:00
    // 501,5,,RETURN-BIN,Returns Processing Bin,Temporary bin for returned items,Pcs,50.00,5.00,TRUE,2024-02-01 13:15:00,2024-06-24 13:26:00
  }

}