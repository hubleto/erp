<?php

namespace CeremonyCrmMod\Core\Invoices;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public string $translationContext = 'mod.core.invoices.loader';

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

    $this->app->sidebar->addLink(1, 85100, 'invoices', $this->translate('Invoices'), 'fas fa-euro-sign');
  }

  public function installTables()
  {
    $mInvoice = new \CeremonyCrmMod\Core\Invoices\Models\Invoice($this->app);
    $mInvoiceItem = new \CeremonyCrmMod\Core\Invoices\Models\InvoiceItem($this->app);

    $mInvoice->dropTableIfExists()->install();
    $mInvoiceItem->dropTableIfExists()->install();
  }

  public function installDefaultPermissions()
  {
    $mPermission = new \CeremonyCrmMod\Core\Settings\Models\Permission($this->app);
    $permissions = [
      "CeremonyCrmMod/Core/Invoices/Models/Invoice:Create,Read,Update,Delete",
      "CeremonyCrmMod/Core/Invoices/Models/InvoiceItem:Create,Read,Update,Delete",
      "CeremonyCrmMod/Core/Invoices/Controllers/Print",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}