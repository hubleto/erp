<?php

namespace CeremonyCrmApp\Modules\Core\Invoices;

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
  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 85100, 'invoices', $this->app->translate('Invoices'), 'fas fa-euro-sign');
  }

  public function installTables()
  {
    $mInvoice = new \CeremonyCrmApp\Modules\Core\Invoices\Models\Invoice($this->app);
    $mInvoiceItem = new \CeremonyCrmApp\Modules\Core\Invoices\Models\InvoiceItem($this->app);

    $mInvoice->dropTableIfExists()->install();
    $mInvoiceItem->dropTableIfExists()->install();
  }

  public function installDefaultPermissions()
  {
    $mPermission = new \CeremonyCrmApp\Modules\Core\Settings\Models\Permission($this->app);
    $permissions = [
      "CeremonyCrmApp/Modules/Core/Invoices/Models/Invoice:Create,Read,Update,Delete",
      "CeremonyCrmApp/Modules/Core/Invoices/Models/InvoiceItem:Create,Read,Update,Delete",
      "CeremonyCrmApp/Modules/Core/Invoices/Controllers/Print",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}