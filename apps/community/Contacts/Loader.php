<?php

namespace HubletoApp\Community\Contacts;

class Loader extends \HubletoMain\Core\App
{
  const DEFAULT_INSTALLATION_CONFIG = [
    'sidebarOrder' => 0,
  ];

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^contacts\/?$/' => Controllers\Persons::class,
      '/^contacts\/get-customer-contacts\/?$/' => Controllers\Api\GetCustomerContacts::class,

      '/^settings\/contact-categories\/?$/' => Controllers\ContactCategories::class,
    ]);

    $this->setConfigAsInteger('sidebarOrder', 0);

    $this->main->addSetting(['title' => $this->translate('Contact Categories'), 'icon' => 'fas fa-phone', 'url' => 'settings/contact-categories']);
  }


  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mContactCategory = new Models\ContactCategory($this->main);
      $mPerson = new Models\Person($this->main);
      $mContact = new Models\Contact($this->main);
      $mPersonTag = new Models\PersonTag($this->main);

      $mContactCategory->dropTableIfExists()->install();
      $mPerson->dropTableIfExists()->install();
      $mContact->dropTableIfExists()->install();
      $mPersonTag->dropTableIfExists()->install();

      $mContactCategory->eloquent->create([ 'name' => 'Work' ]);
      $mContactCategory->eloquent->create([ 'name' => 'Home' ]);
      $mContactCategory->eloquent->create([ 'name' => 'Other' ]);
    }
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