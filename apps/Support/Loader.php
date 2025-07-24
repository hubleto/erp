<?php

namespace HubletoApp\Community\Support;

class Loader extends \Hubleto\Framework\App
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
  //   $mPermission = $this->main->di->create(\HubletoApp\Community\Settings\Models\Permission::class);
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
