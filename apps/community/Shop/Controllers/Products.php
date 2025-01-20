<?php

namespace HubletoApp\Community\Shop\Controllers;

class Products extends \HubletoMain\Core\Controller {

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'shop', 'content' => $this->translate('Shop') ],
      [ 'url' => '', 'content' => $this->translate('Products') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/community/Shop/Views/Products.twig');
  }
}