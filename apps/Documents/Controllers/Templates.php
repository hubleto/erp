<?php

namespace Hubleto\App\Community\Documents\Controllers;

class Templates extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'documents', 'content' => $this->translate('Documents') ],
      [ 'url' => 'templates', 'content' => $this->translate('Templates') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Documents/Templates.twig');
  }

}
