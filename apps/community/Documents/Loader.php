<?php

namespace HubletoApp\Community\Documents;

class Loader extends \HubletoMain\Core\App
{

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);

    $this->registerModel(Models\Document::class);
  }

  public function init(): void
  {
    $this->main->router->httpGet([
      '/^documents\/?$/' => Controllers\Documents::class,
    ]);

    $this->main->sidebar->addLink(1, 700, 'documents', $this->translate('Documents'), 'fa-regular fa-file', str_starts_with($this->main->requestedUri, 'documents'));
  }

  public function installTables()
  {
    $mDocuments = new \HubletoApp\Community\Documents\Models\Document($this->main);
    $mDocuments->dropTableIfExists()->install();
  }

  public function installDefaultPermissions()
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [
      "HubletoApp/Community/Documents/Models/Document:Create" => "Document/Create",
      "HubletoApp/Community/Documents/Models/Document:Read" => "Document/Read",
      "HubletoApp/Community/Documents/Models/Document:Update" => "Document/Update",
      "HubletoApp/Community/Documents/Models/Document:Delete" => "Document/Delete",

      "HubletoApp/Community/Documents/Controllers/Documents" => "Document/Controller",

      "HubletoApp/Community/Documents/Documents" => "Document",
    ];

    foreach ($permissions as $permission => $allias) {
      $mPermission->eloquent->create([
        "permission" => $permission,
        "allias" => $allias,
      ]);
    }
  }
}