<?php

namespace HubletoApp\Community\Dashboard;

class Loader extends \HubletoMain\Core\App
{

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^$/' => Controllers\Home::class,
    ]);
  }

  public function installDefaultPermissions(): void
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [
      "HubletoApp/Community/Dashboard/Home",
      "HubletoApp/Community/Dashboard/Controllers/Home",
    ];

    foreach ($permissions as $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}