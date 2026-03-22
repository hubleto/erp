<?php

namespace Hubleto\App\Community\Products;

class Loader extends \Hubleto\Erp\App
{

  public array $productTypes = [];

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
      '/^products\/?$/' => Controllers\Products::class,
      '/^products\/add?\/?$/' => ['controller' => Controllers\Products::class, 'vars' => [ 'recordId' => -1 ]],
      '/^products(\/(?<recordId>\d+))?\/?$/' => Controllers\Products::class,

      '/^products\/categories(\/(?<recordId>\d+))?\/?$/' => Controllers\Categories::class,
      '/^products\/categories\/add?\/?$/' => ['controller' => Controllers\Categories::class, 'vars' => [ 'recordId' => -1 ]],

      '/^products\/groups(\/(?<recordId>\d+))?\/?$/' => Controllers\Groups::class,
      '/^products\/groups\/add?\/?$/' => ['controller' => Controllers\Groups::class, 'vars' => [ 'recordId' => -1 ]],
    ]);

    $appMenu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    $appMenu->addItem($this, 'products', $this->translate('Products'), 'fas fa-cart-shopping');
    $appMenu->addItem($this, 'products/groups', $this->translate('Groups'), 'fas fa-burger');
    $appMenu->addItem($this, 'products/categories', $this->translate('Categories'), 'fas fa-tag');

    $this->productTypes = $this->collectExtendibles('ProductTypes');
  }

  /**
   * [Description for upgradeSchema]
   *
   * @param int $round
   * 
   * @return void
   * 
   */
  public function installApp(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Group::class)->upgradeSchema();
      $this->getModel(Models\Category::class)->upgradeSchema();
      $this->getModel(Models\Product::class)->upgradeSchema();
      $this->getModel(Models\ProductSupplier::class)->upgradeSchema();
    }
  }

  /**
   * [Description for generateDemoData]
   *
   * @return void
   * 
   */
  public function generateDemoData(): void
  {

    $categories = [
      [ 'id' => 100, 'id_parent' => 0, 'name' => $this->translate('Computers & Accessories') ],
      [ 'id' => 110, 'id_parent' => 100, 'name' => $this->translate('Laptops & Desktops') ],
      [ 'id' => 111, 'id_parent' => 110, 'name' => $this->translate('Laptop Computers') ],
      [ 'id' => 112, 'id_parent' => 110, 'name' => $this->translate('Desktop Computers') ],
      [ 'id' => 113, 'id_parent' => 110, 'name' => $this->translate('Monitors') ],
      [ 'id' => 120, 'id_parent' => 100, 'name' => $this->translate('Components & Storage') ],
      [ 'id' => 121, 'id_parent' => 120, 'name' => $this->translate('Internal Components') ],
      [ 'id' => 122, 'id_parent' => 120, 'name' => $this->translate('Hard Drives & SSDs') ],
      [ 'id' => 123, 'id_parent' => 120, 'name' => $this->translate('Networking') ],
      [ 'id' => 130, 'id_parent' => 100, 'name' => $this->translate('Peripherals & Accessories') ],
      [ 'id' => 131, 'id_parent' => 130, 'name' => $this->translate('Keyboards & Mice') ],
      [ 'id' => 132, 'id_parent' => 130, 'name' => $this->translate('Printers & Scanners') ],
      [ 'id' => 133, 'id_parent' => 130, 'name' => $this->translate('Webcams & Microphones') ],
      [ 'id' => 200, 'id_parent' => 0, 'name' => $this->translate('Mobile Devices & Communication') ],
      [ 'id' => 210, 'id_parent' => 200, 'name' => $this->translate('Smartphones & Cell Phones') ],
      [ 'id' => 211, 'id_parent' => 210, 'name' => $this->translate('Unlocked Phones') ],
      [ 'id' => 212, 'id_parent' => 210, 'name' => $this->translate('Contract Phones') ],
      [ 'id' => 213, 'id_parent' => 210, 'name' => $this->translate('Basic/Feature Phones') ],
      [ 'id' => 220, 'id_parent' => 200, 'name' => $this->translate('Tablets & E-Readers') ],
      [ 'id' => 221, 'id_parent' => 220, 'name' => $this->translate('Android Tablets') ],
      [ 'id' => 222, 'id_parent' => 220, 'name' => $this->translate('iPad Tablets') ],
      [ 'id' => 230, 'id_parent' => 200, 'name' => $this->translate('Mobile Accessories') ],
      [ 'id' => 231, 'id_parent' => 230, 'name' => $this->translate('Cases & Screen Protectors') ],
      [ 'id' => 232, 'id_parent' => 230, 'name' => $this->translate('Chargers & Power Banks') ],
      [ 'id' => 233, 'id_parent' => 230, 'name' => $this->translate('Bluetooth Headsets & Car Kits') ],
      [ 'id' => 300, 'id_parent' => 0, 'name' => $this->translate('Audio & Home Theater') ],
      [ 'id' => 310, 'id_parent' => 300, 'name' => $this->translate('Home Audio') ],
      [ 'id' => 311, 'id_parent' => 310, 'name' => $this->translate('Speakers') ],
      [ 'id' => 312, 'id_parent' => 310, 'name' => $this->translate('Home Theater Systems') ],
      [ 'id' => 313, 'id_parent' => 310, 'name' => $this->translate('Receivers & Amplifiers') ],
      [ 'id' => 320, 'id_parent' => 300, 'name' => $this->translate('Headphones & Portable Audio') ],
      [ 'id' => 321, 'id_parent' => 320, 'name' => $this->translate('Over-Ear & On-Ear Headphones') ],
      [ 'id' => 322, 'id_parent' => 320, 'name' => $this->translate('Earbuds & In-Ear Monitors') ],
      [ 'id' => 323, 'id_parent' => 320, 'name' => $this->translate('Portable Bluetooth Speakers') ],
      [ 'id' => 330, 'id_parent' => 300, 'name' => $this->translate('Televisions & Video') ],
      [ 'id' => 331, 'id_parent' => 330, 'name' => $this->translate('Smart TVs') ],
      [ 'id' => 332, 'id_parent' => 330, 'name' => $this->translate('Projectors & Screens') ],
      [ 'id' => 333, 'id_parent' => 330, 'name' => $this->translate('Streaming Media Players') ],
      [ 'id' => 400, 'id_parent' => 0, 'name' => $this->translate('Smart Home & Security') ],
      [ 'id' => 410, 'id_parent' => 400, 'name' => $this->translate('Smart Home Devices') ],
      [ 'id' => 411, 'id_parent' => 410, 'name' => $this->translate('Smart Speakers & Displays') ],
      [ 'id' => 412, 'id_parent' => 410, 'name' => $this->translate('Smart Lighting & Plugs') ],
      [ 'id' => 413, 'id_parent' => 410, 'name' => $this->translate('Smart Thermostats & HVAC') ],
      [ 'id' => 420, 'id_parent' => 400, 'name' => $this->translate('Security & Surveillance') ],
      [ 'id' => 421, 'id_parent' => 420, 'name' => $this->translate('Security Cameras') ],
      [ 'id' => 422, 'id_parent' => 420, 'name' => $this->translate('Video Doorbells') ],
      [ 'id' => 423, 'id_parent' => 420, 'name' => $this->translate('Home Alarm Systems') ],
      [ 'id' => 500, 'id_parent' => 0, 'name' => $this->translate('Cameras & Drones') ],
      [ 'id' => 510, 'id_parent' => 500, 'name' => $this->translate('Digital Cameras') ],
      [ 'id' => 511, 'id_parent' => 510, 'name' => $this->translate('DSLR Cameras') ],
      [ 'id' => 512, 'id_parent' => 510, 'name' => $this->translate('Mirrorless Cameras') ],
      [ 'id' => 513, 'id_parent' => 510, 'name' => $this->translate('Point & Shoot Cameras') ],
      [ 'id' => 520, 'id_parent' => 500, 'name' => $this->translate('Video & Action Cameras') ],
      [ 'id' => 521, 'id_parent' => 520, 'name' => $this->translate('Camcorders') ],
      [ 'id' => 522, 'id_parent' => 520, 'name' => $this->translate('Action Cameras') ],
      [ 'id' => 530, 'id_parent' => 500, 'name' => $this->translate('Drones & Accessories') ],
      [ 'id' => 531, 'id_parent' => 530, 'name' => $this->translate('Camera Drones') ],
      [ 'id' => 532, 'id_parent' => 530, 'name' => $this->translate('Drone Parts') ],
      [ 'id' => 600, 'id_parent' => 0, 'name' => $this->translate('Gaming') ],
      [ 'id' => 610, 'id_parent' => 600, 'name' => $this->translate('Gaming Consoles') ],
      [ 'id' => 611, 'id_parent' => 610, 'name' => $this->translate('PlayStation Consoles') ],
      [ 'id' => 612, 'id_parent' => 610, 'name' => $this->translate('Xbox Consoles') ],
      [ 'id' => 613, 'id_parent' => 610, 'name' => $this->translate('Nintendo Consoles') ],
      [ 'id' => 620, 'id_parent' => 600, 'name' => $this->translate('PC Gaming') ],
      [ 'id' => 621, 'id_parent' => 620, 'name' => $this->translate('Gaming Headsets') ],
      [ 'id' => 622, 'id_parent' => 620, 'name' => $this->translate('Gaming Keyboards & Mice') ],
      [ 'id' => 623, 'id_parent' => 620, 'name' => $this->translate('PC Game Controllers') ],
      [ 'id' => 630, 'id_parent' => 600, 'name' => $this->translate('Games & Accessories') ],
      [ 'id' => 631, 'id_parent' => 630, 'name' => $this->translate('Console Games') ],
      [ 'id' => 632, 'id_parent' => 630, 'name' => $this->translate('VR/AR Headsets & Gear') ],
      [ 'id' => 700, 'id_parent' => 0, 'name' => $this->translate('Wearable Technology') ],
      [ 'id' => 710, 'id_parent' => 700, 'name' => $this->translate('Smartwatches') ],
      [ 'id' => 711, 'id_parent' => 710, 'name' => $this->translate('Apple Watch') ],
      [ 'id' => 712, 'id_parent' => 710, 'name' => $this->translate('Android/Other Smartwatches') ],
      [ 'id' => 720, 'id_parent' => 700, 'name' => $this->translate('Fitness Trackers') ],
      [ 'id' => 721, 'id_parent' => 720, 'name' => $this->translate('Activity Bands') ],
      [ 'id' => 722, 'id_parent' => 720, 'name' => $this->translate('Smart Scales') ],
      [ 'id' => 730, 'id_parent' => 700, 'name' => $this->translate('Wearable Accessories') ],
      [ 'id' => 731, 'id_parent' => 730, 'name' => $this->translate('Watch Bands') ],
      [ 'id' => 732, 'id_parent' => 730, 'name' => $this->translate('Replacement Chargers') ],
    ];

    $products = [
      [ 'id_category' => 110, 'name' => 'MacBook Air, Dell XPS Laptop, HP All-in-One PC, Custom Gaming Desktop Tower' ],
      [ 'id_category' => 110, 'name' => 'Gaming Laptops, Business Notebooks, Ultrabooks' ],
      [ 'id_category' => 110, 'name' => 'Mini PCs, All-in-One PCs, Desktop Towers' ],
      [ 'id_category' => 110, 'name' => '4K Gaming Monitor, Curved Ultrawide Display, Portable USB-C Monitor' ],
      [ 'id_category' => 120, 'name' => 'AMD Ryzen CPU, NVIDIA GeForce RTX GPU, DDR5 RAM Modules, Motherboard' ],
      [ 'id_category' => 120, 'name' => '2TB External SSD, Internal M.2 NVMe SSD, Network Attached Storage (NAS) Drives' ],
      [ 'id_category' => 120, 'name' => 'Wi-Fi 6 Router, Mesh Wi-Fi System, Ethernet Switch, Powerline Adapter' ],
      [ 'id_category' => 130, 'name' => 'Mechanical Gaming Keyboard, Ergonomic Wireless Mouse, Mouse Pads' ],
      [ 'id_category' => 130, 'name' => 'All-in-One Inkjet Printer, Document Scanner, Label Printer' ],
      [ 'id_category' => 130, 'name' => '4K Webcam, USB Condenser Microphone, Ring Light Kit' ],
      [ 'id_category' => 210, 'name' => 'iPhone 16 Pro, Samsung Galaxy S25, Google Pixel 9' ],
      [ 'id_category' => 210, 'name' => 'Flagship 5G Handsets, Budget Android Phones' ],
      [ 'id_category' => 210, 'name' => 'Carrier-specific phones (e.g., Verizon, AT&T)' ],
      [ 'id_category' => 210, 'name' => 'Flip Phones, Simple Seniors Phone' ],
      [ 'id_category' => 220, 'name' => 'iPad Pro, Samsung Galaxy Tab, Amazon Kindle Oasis' ],
      [ 'id_category' => 220, 'name' => 'High-end and budget Android tablets' ],
      [ 'id_category' => 220, 'name' => 'iPad Mini, iPad Air, Smart Keyboard Folio' ],
      [ 'id_category' => 230, 'name' => 'Rugged Phone Case, Tempered Glass Screen Protector, Tablet Sleeve' ],
      [ 'id_category' => 230, 'name' => '65W GaN Wall Charger, 20,000mAh Power Bank, Wireless Charging Pad' ],
      [ 'id_category' => 230, 'name' => 'Noise-Cancelling Bluetooth Headset, MagSafe Car Mount' ],
      [ 'id_category' => 310, 'name' => 'Sonos Arc Soundbar, Bookshelf Speakers, In-Wall Speakers, Subwoofers' ],
      [ 'id_category' => 310, 'name' => '5.1 Channel Surround System, AV Receiver + Speaker Packages' ],
      [ 'id_category' => 310, 'name' => '7.2 Channel AV Receiver, Stereo Integrated Amplifier' ],
      [ 'id_category' => 320, 'name' => 'Sony WH-1000XM6, Wired Studio Headphones' ],
      [ 'id_category' => 320, 'name' => 'Apple AirPods Pro, Samsung Galaxy Buds, Wired IEMs' ],
      [ 'id_category' => 320, 'name' => 'JBL Flip, Ultimate Ears BOOM, Shower Speakers' ],
      [ 'id_category' => 330, 'name' => 'LG C5 OLED TV, Samsung QLED 4K TV, Budget 1080p TVs' ],
      [ 'id_category' => 330, 'name' => '4K Laser Projector, Motorized Projector Screen' ],
      [ 'id_category' => 330, 'name' => 'Roku Ultra, Apple TV 4K, Amazon Fire Stick' ],
      [ 'id_category' => 410, 'name' => 'Amazon Echo Show, Google Nest Hub, HomePod Mini' ],
      [ 'id_category' => 410, 'name' => 'Philips Hue Smart Bulbs, Smart Wall Plugs, Wi-Fi Dimmers' ],
      [ 'id_category' => 410, 'name' => 'Nest Learning Thermostat, Ecobee Smart Thermostat' ],
      [ 'id_category' => 420, 'name' => 'Ring Floodlight Cam, Arlo Pro Wireless Cameras (Indoor/Outdoor)' ],
      [ 'id_category' => 420, 'name' => 'Ring Video Doorbell Pro, Google Nest Doorbell' ],
      [ 'id_category' => 420, 'name' => 'SimpliSafe Wireless Home Security Kit, Window/Door Sensors' ],
      [ 'id_category' => 510, 'name' => 'Canon EOS R7, Nikon Z6 II, Sony a7 IV' ],
      [ 'id_category' => 510, 'name' => 'Entry-level DSLR Kit, Professional Full-Frame DSLR Body' ],
      [ 'id_category' => 510, 'name' => 'APS-C Mirrorless Camera, Full-Frame Mirrorless Body' ],
      [ 'id_category' => 510, 'name' => 'Premium Compact Cameras, Travel Zoom Cameras' ],
      [ 'id_category' => 520, 'name' => '4K Video Camera, Professional Broadcast Camcorder' ],
      [ 'id_category' => 520, 'name' => 'GoPro HERO 13 Black, DJI Osmo Action' ],
      [ 'id_category' => 530, 'name' => 'DJI Mini 4 Pro, Professional Filmmaking Drone' ],
      [ 'id_category' => 530, 'name' => 'Replacement Batteries, Propeller Guards, Landing Pads' ],
      [ 'id_category' => 610, 'name' => 'PlayStation 5 Slim, Xbox Series X, Nintendo Switch OLED' ],
      [ 'id_category' => 610, 'name' => 'PS5 DualSense Controller, PlayStation VR2' ],
      [ 'id_category' => 610, 'name' => 'Xbox Elite Wireless Controller, Xbox Game Pass Subscription' ],
      [ 'id_category' => 610, 'name' => 'Nintendo Switch Pro Controller, Joy-Con Grips' ],
      [ 'id_category' => 620, 'name' => 'HyperX Cloud, Astro A40, Wireless PC Gaming Headsets' ],
      [ 'id_category' => 620, 'name' => 'Mechanical RGB Gaming Keyboard, Lightweight Gaming Mouse' ],
      [ 'id_category' => 620, 'name' => 'USB Gamepad, Racing Wheels and Pedals' ],
      [ 'id_category' => 630, 'name' => 'New release PS5/Xbox/Switch titles (physical and digital codes)' ],
      [ 'id_category' => 630, 'name' => 'Meta Quest 3, Valve Index Headset' ],
      [ 'id_category' => 710, 'name' => 'Apple Watch Series 10, Samsung Galaxy Watch 7' ],
      [ 'id_category' => 710, 'name' => 'Apple Watch SE, Milanese Loop Band' ],
      [ 'id_category' => 710, 'name' => 'Google Pixel Watch, Fossil Gen 7 Smartwatch' ],
      [ 'id_category' => 720, 'name' => 'Fitbit Charge 6, Garmin Vivofit, Activity Rings' ],
      [ 'id_category' => 720, 'name' => 'Basic Step Trackers, Water-resistant Swim Trackers' ],
      [ 'id_category' => 720, 'name' => 'Wi-Fi Body Composition Scale (e.g., Withings, Fitbit)' ],
      [ 'id_category' => 730, 'name' => 'Leather Watch Straps, Silicone Sport Bands' ],
      [ 'id_category' => 730, 'name' => 'Wireless Charging Puck, Multi-Device Charging Stand' ],
    ];

    $mCategory = $this->getModel(Models\Category::class);
    $mProduct = $this->getModel(Models\Product::class);

    foreach ($categories as $category) {
      $mCategory->record->recordCreate($category, true);
    }
    
    foreach ($products as $product) {
      $mProduct->record->recordCreate($product);
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
        "description" => $product['GROUP']['title'] ?? '',
      ];
    }

    return $results;
  }
}
