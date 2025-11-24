<?php

namespace Hubleto\App\Community\Documents\Controllers;

class Folders extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      // [ 'url' => 'documents', 'content' => $this->translate('Documents') ],
      [ 'url' => 'documents/folders', 'content' => $this->translate('Folders') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Documents/Folders.twig');
  }

}
