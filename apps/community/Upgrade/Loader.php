<?php

namespace HubletoApp\Community\Upgrade;

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
    $this->main->router->httpGet([
      '/^upgrade\/?$/' => Controllers\Upgrade::class,
      '/^you-are-pro\/?$/' => Controllers\YouArePro::class,
    ]);

    // if (!$this->main->isPro) {
    //   $this->main->sidebar->addLink(1, 1000, 'upgrade', $this->translate('Upgrade'), 'fas fa-trophy', str_starts_with($this->main->requestedUri, 'upgrade'));
    // }
  }
  public function installDefaultPermissions()
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [

      "HubletoApp/Community/Upgrade/Controllers/Upgrade",
      "HubletoApp/Community/Upgrade/Controllers/YouArePro",

      "HubletoApp/Community/Upgrade/Upgrade",
      "HubletoApp/Community/Upgrade/YouArePro",
    ];

    foreach ($permissions as $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }

}