<?php

namespace HubletoApp\Community\Dashboard;

class Loader extends \HubletoMain\Core\App
{


  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
  }

  public function init(): void
  {
    $this->main->router->httpGet([
      '/^$/' => Controllers\Home::class,
    ]);

    $this->main->sidebar->addLink(1, 0, '', $this->translate('Home'), 'fas fa-home', $this->main->requestedUri == '');

  }
  public function installDefaultPermissions()
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [
      "HubletoApp/Community/Dashboard/Home" => "Dashboard",
      "HubletoApp/Community/Dashboard/Controllers/Home" => "Dashboard/Controller",
    ];

    foreach ($permissions as $permission => $allias) {
      $mPermission->eloquent->create([
        "permission" => $permission,
        "allias" => $allias,
      ]);
    }
  }
}