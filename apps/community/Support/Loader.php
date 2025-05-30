<?php

namespace HubletoApp\Community\Support;

class Loader extends \HubletoMain\Core\App
{

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^support\/?$/' => Controllers\Dashboard::class,
    ]);

  }

  // public function installDefaultPermissions(): void
  // {
  //   $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
  //   $permissions = [

  //     "HubletoApp/Community/Support/Controllers/Dashboard",

  //     "HubletoApp/Community/Support/Dashboard",
  //   ];

  //   foreach ($permissions as $permission) {
  //     $mPermission->record->recordCreate([
  //       "permission" => $permission
  //     ]);
  //   }
  // }

}