<?php

namespace Hubleto\App\Community\Documents;

class Loader extends \Hubleto\Framework\App
{
  
  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^documents\/api\/get-folder-content\/?$/' => Controllers\Api\GetFolderContent::class,

      '/^documents\/?$/' => Controllers\Browse::class,
      '/^documents\/browse\/?$/' => Controllers\Browse::class,
      '/^documents\/list\/?$/' => Controllers\Documents::class,

      '/^documents(\/(?<recordId>\d+))?\/?$/' => Controllers\Documents::class,
      '/^documents\/add\/?$/' => ['controller' => Controllers\Documents::class, 'vars' => ['recordId' => -1]],

      '/^documents\/templates(\/(?<recordId>\d+))?\/?$/' => Controllers\Templates::class,
      '/^documents\/templates\/add\/?$/' => ['controller' => Controllers\Templates::class, 'vars' => ['recordId' => -1]],
    ]);

  }

  public function getRootFolderId(): int|null
  {
    $mFolder = $this->getModel(Models\Folder::class);
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
      $mFolder = $this->getModel(Models\Folder::class);
      $mFolder->dropTableIfExists()->install();

      $mFolder->record->recordCreate([
        'id_parent_folder' => null,
        'uid' => '_ROOT_',
        'name' => '_ROOT_',
      ]);

      $this->getModel(Models\Document::class)->dropTableIfExists()->install();
      $this->getModel(Models\Template::class)->dropTableIfExists()->install();
    }

  }

  public function generateDemoData(): void
  {
    $mFolder = $this->getModel(Models\Folder::class);
    $mDocument = $this->getModel(Models\Document::class);
    $mTemplate = $this->getModel(Models\Template::class);

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

    $templates = [
      'en-quotation' => ['name' => 'Quotation', 'file' => 'en/Quotation.twig'],
      'en-invoice-proforma' => ['name' => 'Proforma invoice', 'file' => 'en/InvoiceProforma.twig'],
      'en-invoice-advance' => ['name' => 'Advance invoice', 'file' => 'en/InvoiceAdvance.twig'],
      'en-invoice-standard' => ['name' => 'Standard invoice', 'file' => 'en/InvoiceStandard.twig'],
      'en-invoice-credit-note' => ['name' => 'Credit note', 'file' => 'en/InvoiceCreditNote.twig'],
      'en-invoice-debit-note' => ['name' => 'Debit note', 'file' => 'en/InvoiceDebitNote.twig'],
      'sk-invoice-proforma' => ['name' => 'Proforma faktúra', 'file' => 'sk/InvoiceProforma.twig'],
      'sk-invoice-advance' => ['name' => 'Zálohová faktúra', 'file' => 'sk/InvoiceAdvance.twig'],
      'sk-invoice-standard' => ['name' => 'Faktúra', 'file' => 'sk/InvoiceStandard.twig'],
      'sk-invoice-credit-note' => ['name' => 'Dobropis', 'file' => 'sk/InvoiceCreditNote.twig'],
      'sk-invoice-debit-note' => ['name' => 'Ťarchopis', 'file' => 'sk/InvoiceDebitNote.twig'],
    ];

    foreach ($templates as $usedFor => $templateData) {
      $mTemplate->record->recordCreate([
        'name' => $templateData['name'],
        'used_for' => $usedFor,
        'content' => file_get_contents(__DIR__ . '/DefaultTemplates/' . $templateData['file']),
      ]);
    }

  }

}
