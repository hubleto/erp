<?php

namespace HubletoApp\Community\Documents;

class Loader extends \HubletoMain\Core\App
{

  // public function __construct(\HubletoMain $main)
  // {
  //   parent::__construct($main);

  //   $this->registerModel(Models\Document::class);
  // }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^documents\/?$/' => Controllers\Documents::class,
    ]);

    $this->main->sidebar->addLink(1, 700, 'documents', $this->translate('Documents'), 'fa-regular fa-file', str_starts_with($this->main->requestedUri, 'documents'));
  }

  public function installTables(): void
  {
    $mDocuments = new \HubletoApp\Community\Documents\Models\Document($this->main);
    $mDocuments->dropTableIfExists()->install();
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
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}