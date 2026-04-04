<?php

namespace Hubleto\App\Community\Documents\Controllers;

class FileBrowser extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'documents/files', 'content' => $this->translate('Browse files') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Documents/FileBrowser.twig');
  }

}
