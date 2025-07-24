<?php

namespace HubletoApp\Community\Documents;

class Loader extends \Hubleto\Framework\App
{
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^documents\/?$/' => Controllers\Browse::class,
      '/^documents\/browse\/?$/' => Controllers\Browse::class,
      '/^documents\/list\/?$/' => Controllers\Table::class,
      '/^documents\/api\/get-folder-content\/?$/' => Controllers\Api\GetFolderContent::class,
    ]);

    $appMenu = $this->main->apps->community('Desktop')->appMenu;
    $appMenu->addItem($this, 'documents/browse', $this->translate('Browse'), 'fas fa-table');
    $appMenu->addItem($this, 'documents/list', $this->translate('List'), 'fas fa-list');
  }

  public function getRootFolderId(): int|null
  {
    $mFolder = $this->main->di->create(Models\Folder::class);
    $rootFolder = $mFolder->record->where('uid', '_ROOT_')->first()->toArray();
    if (!isset($rootFolder['id'])) {
      return null;
    } else {
      return (int) $rootFolder['id'];
    }
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mFolder = $this->main->di->create(Models\Folder::class);
      $mFolder->dropTableIfExists()->install();
      (new Models\Document($this->main))->dropTableIfExists()->install();

      $mFolder->record->recordCreate([
        'id_parent_folder' => null,
        'uid' => '_ROOT_',
        'name' => '_ROOT_',
      ]);

    }
  }

  // public function installDefaultPermissions(): void
  // {
  //   $mPermission = $this->main->di->create(\HubletoApp\Community\Settings\Models\Permission::class);
  //   $permissions = [
  //     "HubletoApp/Community/Documents/Models/Document:Create",
  //     "HubletoApp/Community/Documents/Models/Document:Read",
  //     "HubletoApp/Community/Documents/Models/Document:Update",
  //     "HubletoApp/Community/Documents/Models/Document:Delete",

  //     "HubletoApp/Community/Documents/Controllers/Documents",

  //     "HubletoApp/Community/Documents/Documents",
  //   ];

  //   foreach ($permissions as $permission) {
  //     $mPermission->record->recordCreate([
  //       "permission" => $permission
  //     ]);
  //   }
  // }

  public function generateDemoData(): void
  {
    $mFolder = $this->main->di->create(Models\Folder::class);
    $mDocument = $this->main->di->create(Models\Document::class);

    $mDocument->record->recordCreate([
      'id_folder' => $this->getRootFolderId(),
      'name' => 'bid_template.docx',
      'hyperlink' => 'https://www.google.com',
    ]);

    $idFolderMM = $mFolder->record->recordCreate([ 'id_parent_folder' => $this->getRootFolderId(), 'name' => 'Marketing materials' ])['id'];
    $idFolderMM1 = $mFolder->record->recordCreate(['id_parent_folder' => $idFolderMM, 'name' => 'LinkedIn'])['id'];
    $idFolderMM2 = $mFolder->record->recordCreate(['id_parent_folder' => $idFolderMM, 'name' => 'GoogleAds'])['id'];

    $idFolderCU = $mFolder->record->recordCreate([ 'id_parent_folder' => $this->getRootFolderId(), 'name' => 'Customer profiles' ])['id'];

    $mDocument->record->recordCreate([ 'id_folder' => $idFolderMM, 'name' => 'logo.png', 'hyperlink' => 'https://www.google.com' ]);
    $mDocument->record->recordCreate([ 'id_folder' => $idFolderMM1, 'name' => 'post_image_1.png', 'hyperlink' => 'https://www.google.com' ]);
    $mDocument->record->recordCreate([ 'id_folder' => $idFolderMM1, 'name' => 'post_image_2.png', 'hyperlink' => 'https://www.google.com' ]);
    $mDocument->record->recordCreate([ 'id_folder' => $idFolderMM2, 'name' => 'analytics_report_1.pdf', 'hyperlink' => 'https://www.google.com' ]);
    $mDocument->record->recordCreate([ 'id_folder' => $idFolderMM2, 'name' => 'analytics_report_2.pdf', 'hyperlink' => 'https://www.google.com' ]);

    $mDocument->record->recordCreate([ 'id_folder' => $idFolderCU, 'name' => 'customer_profile_1.pdf', 'hyperlink' => 'https://www.google.com' ]);
    $mDocument->record->recordCreate([ 'id_folder' => $idFolderCU, 'name' => 'customer_profile_2.pdf', 'hyperlink' => 'https://www.google.com' ]);
  }

}
