<?php

namespace CeremonyCrmMod\Invoices;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public function __construct(\CeremonyCrmApp $app)
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
    $mInvoice = new \CeremonyCrmMod\Invoices\Models\Invoice($this->app);
    $mInvoiceItem = new \CeremonyCrmMod\Invoices\Models\InvoiceItem($this->app);

    $mInvoice->dropTableIfExists()->install();
    $mInvoiceItem->dropTableIfExists()->install();
  }

  public function installDefaultPermissions()
  {
    $mPermission = new \CeremonyCrmMod\Settings\Models\Permission($this->app);
    $permissions = [
      "CeremonyCrmMod/Invoices/Models/Invoice:Create,Read,Update,Delete",
      "CeremonyCrmMod/Invoices/Models/InvoiceItem:Create,Read,Update,Delete",
      "CeremonyCrmMod/Invoices/Controllers/Print",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}