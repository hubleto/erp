<?php

namespace HubletoApp\Community\Documents\Controllers;

class Browse extends \HubletoMain\Core\Controllers\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'documents', 'content' => $this->translate('Documents') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Documents/Browse.twig');
  }

}