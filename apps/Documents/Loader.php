<?php

namespace Hubleto\App\Community\Documents;

class Loader extends \Hubleto\Erp\App
{

  /**
   * Inits the app: adds routes, settings, calendars, event listeners, menu items, ...
   *
   * @return void
   *
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^documents\/api\/get-folder-content\/?$/' => Controllers\Api\GetFolderContent::class,

      '/^documents\/?$/' => Controllers\Documents::class,

      '/^documents\/files\/?$/' => Controllers\FileBrowser::class,
      '/^documents\/files\/list\/?$/' => Controllers\Files::class,
      '/^documents\/files\/download\/?$/' => Controllers\DownloadFile::class,

      '/^documents(\/(?<recordId>\d+))?\/?$/' => Controllers\Documents::class,
      '/^documents\/add\/?$/' => ['controller' => Controllers\Documents::class, 'vars' => ['recordId' => -1]],

      '/^documents\/versions(\/(?<recordId>\d+))?\/?$/' => Controllers\DocumentVersions::class,
      '/^documents\/versions\/add\/?$/' => ['controller' => Controllers\DocumentVersions::class, 'vars' => ['recordId' => -1]],

      '/^documents\/reviews(\/(?<recordId>\d+))?\/?$/' => Controllers\DocumentReviews::class,
      '/^documents\/reviews\/add\/?$/' => ['controller' => Controllers\DocumentReviews::class, 'vars' => ['recordId' => -1]],

      '/^documents\/folders\/?(?<recordId>\d+)\/?$/' => Controllers\Folders::class,
      '/^documents\/folders\/add\/?$/' => ['controller' => Controllers\Folders::class, 'vars' => ['recordId' => -1]],

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

  public function installApp(int $round): void
  {
    if ($round == 1) {
      $mFolder = $this->getModel(Models\Folder::class);
      $mFolder->upgradeSchema();

      $mFolder->record->recordCreate([
        'id_parent_folder' => null,
        'uid' => '_ROOT_',
        'name' => '_ROOT_',
      ]);

      $this->getModel(Models\File::class)->upgradeSchema();
      $this->getModel(Models\Document::class)->upgradeSchema();
      $this->getModel(Models\DocumentVersion::class)->upgradeSchema();
      $this->getModel(Models\DocumentReview::class)->upgradeSchema();
      $this->getModel(Models\Template::class)->upgradeSchema();
    }

  }

  public function generateDemoData(): void
  {
    $mFolder = $this->getModel(Models\Folder::class);
    $mFile = $this->getModel(Models\File::class);
    $mTemplate = $this->getModel(Models\Template::class);

    $mFile->record->recordCreate([
      'id_folder' => $this->getRootFolderId(),
      'name' => 'bid_template.docx',
      'hyperlink' => 'https://www.google.com',
    ]);

    $idFolderMM = $mFolder->record->recordCreate([ 'id_parent_folder' => $this->getRootFolderId(), 'name' => $this->translate('Marketing materials') ])['id'];
    $idFolderMM1 = $mFolder->record->recordCreate([ 'id_parent_folder' => $idFolderMM, 'name' => 'LinkedIn' ])['id'];
    $idFolderMM2 = $mFolder->record->recordCreate([ 'id_parent_folder' => $idFolderMM, 'name' => 'GoogleAds' ])['id'];

    $idFolderCU = $mFolder->record->recordCreate([ 'id_parent_folder' => $this->getRootFolderId(), 'name' => $this->translate('Customer profiles') ])['id'];

    $mFile->record->recordCreate([ 'id_folder' => $idFolderMM, 'name' => 'logo.png', 'hyperlink' => 'https://www.google.com' ]);
    $mFile->record->recordCreate([ 'id_folder' => $idFolderMM1, 'name' => 'post_image_1.png', 'hyperlink' => 'https://www.google.com' ]);
    $mFile->record->recordCreate([ 'id_folder' => $idFolderMM1, 'name' => 'post_image_2.png', 'hyperlink' => 'https://www.google.com' ]);
    $mFile->record->recordCreate([ 'id_folder' => $idFolderMM2, 'name' => 'analytics_report_1.pdf', 'hyperlink' => 'https://www.google.com' ]);
    $mFile->record->recordCreate([ 'id_folder' => $idFolderMM2, 'name' => 'analytics_report_2.pdf', 'hyperlink' => 'https://www.google.com' ]);

    $mFile->record->recordCreate([ 'id_folder' => $idFolderCU, 'name' => 'customer_profile_1.pdf', 'hyperlink' => 'https://www.google.com' ]);
    $mFile->record->recordCreate([ 'id_folder' => $idFolderCU, 'name' => 'customer_profile_2.pdf', 'hyperlink' => 'https://www.google.com' ]);

    $templates = [
      'en-quotation' => ['name' => 'Quotation', 'file' => 'en/Quotation.twig'],
      'en-invoice-proforma' => ['name' => 'Proforma invoice', 'file' => 'en/InvoiceProforma.twig'],
      'en-invoice-advance' => ['name' => 'Advance invoice', 'file' => 'en/InvoiceAdvance.twig'],
      'en-invoice-standard' => ['name' => 'Standard invoice', 'file' => 'en/InvoiceStandard.twig'],
      'en-invoice-credit-note' => ['name' => 'Credit note', 'file' => 'en/InvoiceCreditNote.twig'],
      'en-invoice-debit-note' => ['name' => 'Debit note', 'file' => 'en/InvoiceDebitNote.twig'],
      'sk-quotation' => ['name' => 'Ponuka', 'file' => 'sk/Ponuka.twig'],
      'sk-invoice-proforma' => ['name' => 'Proforma faktúra', 'file' => 'sk/InvoiceProforma.twig'],
      'sk-invoice-advance' => ['name' => 'Zálohová faktúra', 'file' => 'sk/InvoiceAdvance.twig'],
      'sk-invoice-standard' => ['name' => 'Vyúčtovacia faktúra s DPH', 'file' => 'sk/VyuctovaciaFakturaSDph.twig'],
      'sk-invoice-credit-note' => ['name' => 'Dobropis s DPH', 'file' => 'sk/DobropisSDph.twig'],
      'sk-invoice-debit-note' => ['name' => 'Ťarchopis', 'file' => 'sk/InvoiceDebitNote.twig'],
    ];

    foreach ($templates as $usedFor => $templateData) {
      $templateFile = __DIR__ . '/DefaultTemplates/' . $templateData['file'];
      if (file_exists($templateFile)) {
        $mTemplate->record->recordCreate([
          'name' => $templateData['name'],
          'used_for' => $usedFor,
          'content' => file_get_contents($templateFile),
        ]);
      }
    }

  }

  /**
   * [Description for renderSecondSidebar]
   *
   * @return string
   * 
   */
  public function renderSecondSidebar(): string
  {
    return '
      <div class="flex flex-col gap-2">
        <a class="btn btn-square btn-primary-outline" href="' . $this->env()->projectUrl . '/documents">
          <span class="icon"><i class="fas fa-file-invoice"></i></span>
          <span class="text">' . $this->translate('Documents') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/documents/versions">
          <span class="icon"><i class="fas fa-list"></i></span>
          <span class="text">' . $this->translate('Versions') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/documents/reviews">
          <span class="icon"><i class="fas fa-list"></i></span>
          <span class="text">' . $this->translate('Reviews') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/documents/files">
          <span class="icon"><i class="fas fa-list"></i></span>
          <span class="text">' . $this->translate('File browser') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/documents/files/list">
          <span class="icon"><i class="fas fa-list"></i></span>
          <span class="text">' . $this->translate('Uploaded files') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/documents/folders">
          <span class="icon"><i class="fas fa-folder"></i></span>
          <span class="text">' . $this->translate('Manage folders') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/documents/templates">
          <span class="icon"><i class="fas fa-file"></i></span>
          <span class="text">' . $this->translate('Templates') . '</span>
        </a>
      </div>
    ';
  }

}
