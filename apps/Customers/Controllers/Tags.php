<?php

namespace HubletoApp\Community\Customers\Controllers;

class Tags extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => '', 'content' => $this->translate('Customer Tags') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Customers/Tags.twig');
  }

}
