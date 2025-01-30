<?php

namespace HubletoApp\Community\Support;

class Loader extends \HubletoMain\Core\App
{
  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
  }

  public function init(): void
  {
    $this->main->router->httpGet([
      '/^support\/?$/' => Controllers\Dashboard::class,
    ]);

    // $this->main->sidebar->addLink(1, 98100, 'support', $this->translate('Support'), 'fas fa-circle-question');
  }

  public function installDefaultPermissions(): void
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [

      "HubletoApp/Community/Support/Controllers/Dashboard",

      "HubletoApp/Community/Support/Dashboard",
    ];

    foreach ($permissions as $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }

}