<?php

namespace HubletoApp\Community\Products\Controllers;

class Groups extends \HubletoMain\Core\Controller {

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'products', 'content' => $this->translate('Products') ],
      [ 'url' => '', 'content' => $this->translate('Product Groups') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/community/Products/Views/Groups.twig');
  }
}