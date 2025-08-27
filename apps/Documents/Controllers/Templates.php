<?php

namespace HubletoApp\Community\Documents\Controllers;

class Templates extends \HubletoMain\Controller
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
    $this->setView('@HubletoApp:Community:Documents/Templates.twig');
  }

}
