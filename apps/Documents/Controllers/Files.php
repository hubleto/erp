<?php

namespace Hubleto\App\Community\Documents\Controllers;

class Files extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'documents/files/browse', 'content' => $this->translate('Files') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Documents/Files.twig');
  }

}
