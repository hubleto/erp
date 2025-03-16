<?php

namespace HubletoApp\Community\Premium;

class Loader extends \HubletoMain\Core\App
{


  // public static function canBeAdded(\HubletoMain $main): bool
  // {
  //   return !$main->isPro;
  // }

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
  }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^premium\/?$/' => Controllers\Premium::class,
      '/^premium\/upgrade\/?$/' => Controllers\Upgrade::class,
      '/^premium\/you-are-upgraded\/?$/' => Controllers\PremiumActivated::class,
    ]);

  }

  public function installDefaultPermissions(): void
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [

      "HubletoApp/Community/Premium/Controllers/Premium",
      "HubletoApp/Community/Premium/Controllers/Upgrade",
      "HubletoApp/Community/Premium/Controllers/PremiumActivated",

      "HubletoApp/Community/Premium/Premium",
      "HubletoApp/Community/Premium/PremiumActivated",
    ];

    foreach ($permissions as $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }

}