<?php

namespace HubletoApp\Community\Help\Controllers;

class Search extends \HubletoMain\Controller
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
    $this->setView('@HubletoApp:Community:Help/Search.twig');
  }

}
