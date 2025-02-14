<?php

namespace HubletoApp\Community\Contacts;

class Loader extends \HubletoMain\Core\App
{

  // public function __construct(\HubletoMain $main)
  // {
  //   parent::__construct($main);

  //   $this->registerModel(Models\Customer::class);
  // }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^contacts\/?$/' => Controllers\Persons::class,
      '/^contacts\/get-customer-contacts\/?$/' => Controllers\Api\GetCustomerContacts::class,
    ]);

    $this->main->sidebar->addLink(1, 41, 'contacts', $this->translate('Contacts'), 'fas fa-person', str_starts_with($this->main->requestedUri, 'contacts'));
  }

  public function installTables(): void
  {

  }

  public function installDefaultPermissions(): void
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [];

    foreach ($permissions as $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}