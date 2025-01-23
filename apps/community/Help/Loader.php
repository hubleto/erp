<?php

namespace HubletoApp\Community\Help;

class Loader extends \HubletoMain\Core\App
{

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
  }

  public function init(): void
  {
    $this->main->router->httpGet([
      '/^help\/?$/' => Controllers\Help::class,
    ]);

    // $this->main->sidebar->addLink(1, 1900, 'help', $this->translate('Help'), 'fas fa-life-ring', str_starts_with($this->main->requestedUri, 'help'));
  }

  public function installDefaultPermissions()
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [

      "HubletoApp/Community/Help/Controllers/Help" => "Help/Controller",

      "HubletoApp/Community/Help/Help" => "Help",
    ];

    foreach ($permissions as $permission => $allias) {
      $mPermission->eloquent->create([
        "permission" => $permission,
        "allias" => $allias,
      ]);
    }
  }

}