<?php

namespace Hubleto\App\Community\Help\Controllers;

class Search extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'help', 'content' => $this->translate('Help') ],
      [ 'url' => 'search', 'content' => $this->translate('Search') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Help/Search.twig');
  }

}
