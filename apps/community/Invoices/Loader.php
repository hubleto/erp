<?php

namespace HubletoApp\Community\Invoices;

class Loader extends \HubletoMain\Core\App
{

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
    $this->registerModel(Models\Invoice::class);
  }

  public function init(): void
  {
    $this->main->router->httpGet([
      '/^invoices\/?$/' => Controllers\Invoices::class,
    ]);

    $this->main->sidebar->addLink(1, 800, 'invoices', $this->translate('Invoices'), 'fas fa-euro-sign', str_starts_with($this->main->requestedUri, 'invoices'));
  }

  public function installTables()
  {
    $mInvoice = new \HubletoApp\Community\Invoices\Models\Invoice($this->main);
    $mInvoiceItem = new \HubletoApp\Community\Invoices\Models\InvoiceItem($this->main);

    $mInvoice->dropTableIfExists()->install();
    $mInvoiceItem->dropTableIfExists()->install();
  }

  public function installDefaultPermissions()
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [

      "HubletoApp/Community/Invoices/Models/Invoice:Create" => "Invoice/Create",
      "HubletoApp/Community/Invoices/Models/Invoice:Read" => "Invoice/Read",
      "HubletoApp/Community/Invoices/Models/Invoice:Update" => "Invoice/Update",
      "HubletoApp/Community/Invoices/Models/Invoice:Delete" => "Invoice/Delete",
      "HubletoApp/Community/Invoices/Models/InvoiceItem:Create" => "InvoiceItem/Create",
      "HubletoApp/Community/Invoices/Models/InvoiceItem:Read" => "InvoiceItem/Read",
      "HubletoApp/Community/Invoices/Models/InvoiceItem:Update" => "InvoiceItem/Update",
      "HubletoApp/Community/Invoices/Models/InvoiceItem:Delete" => "InvoiceItem/Delete",

      "HubletoApp/Community/Invoices/Controllers/Invoices" => "Invoice/Controller",

      "HubletoApp/Community/Invoices/Invoices" => "Invoice",
    ];

    foreach ($permissions as $permission => $allias) {
      $mPermission->eloquent->create([
        "permission" => $permission,
        "allias" => $allias,
      ]);
    }
  }
}