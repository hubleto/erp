<?php

namespace HubletoApp\Invoices;

class Loader extends \HubletoMain\Core\App
{

  public function __construct(\HubletoMain $app)
  {
    parent::__construct($app);
    $this->registerModel(Models\Invoice::class);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^invoices\/?$/' => Controllers\Invoices::class,
    ]);

    $this->app->sidebar->addLink(1, 800, 'invoices', $this->translate('Invoices'), 'fas fa-euro-sign', str_starts_with($this->app->requestedUri, 'invoices'));
  }

  public function installTables()
  {
    $mInvoice = new \HubletoApp\Invoices\Models\Invoice($this->app);
    $mInvoiceItem = new \HubletoApp\Invoices\Models\InvoiceItem($this->app);

    $mInvoice->dropTableIfExists()->install();
    $mInvoiceItem->dropTableIfExists()->install();
  }

  public function installDefaultPermissions()
  {
    $mPermission = new \HubletoApp\Settings\Models\Permission($this->app);
    $permissions = [
      "HubletoApp/Invoices/Models/Invoice:Create,Read,Update,Delete",
      "HubletoApp/Invoices/Models/InvoiceItem:Create,Read,Update,Delete",
      "HubletoApp/Invoices/Controllers/Print",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}