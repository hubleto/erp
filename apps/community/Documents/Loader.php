<?php

namespace HubletoApp\Community\Documents;

class Loader extends \HubletoMain\Core\App
{

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^documents\/?$/' => Controllers\Documents::class,
    ]);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mDocuments = new \HubletoApp\Community\Documents\Models\Document($this->main);
      $mDocuments->dropTableIfExists()->install();
    }
  }

  public function installDefaultPermissions(): void
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [
      "HubletoApp/Community/Documents/Models/Document:Create",
      "HubletoApp/Community/Documents/Models/Document:Read",
      "HubletoApp/Community/Documents/Models/Document:Update",
      "HubletoApp/Community/Documents/Models/Document:Delete",

      "HubletoApp/Community/Documents/Controllers/Documents",

      "HubletoApp/Community/Documents/Documents",
    ];

    foreach ($permissions as $permission) {
      $mPermission->record->recordCreate([
        "permission" => $permission
      ]);
    }
  }
}