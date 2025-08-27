<?php

namespace Hubleto\App\Community\Documents\Controllers;

class Documents extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'documents', 'content' => $this->translate('Documents') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Documents/Documents.twig');
  }

}
