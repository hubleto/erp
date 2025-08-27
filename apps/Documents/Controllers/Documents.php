<?php

namespace HubletoApp\Community\Documents\Controllers;

class Documents extends \HubletoMain\Controller
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
    $this->setView('@HubletoApp:Community:Documents/Documents.twig');
  }

}
