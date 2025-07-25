<?php

namespace HubletoApp\Community\Orders\Controllers;

class States extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'orders', 'content' => $this->translate('Orders') ],
      [ 'url' => '', 'content' => $this->translate('States') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@HubletoApp:Community:Orders/States.twig');

  }

}
