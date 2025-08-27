<?php

namespace HubletoApp\Community\Crypto\Controllers;

class Dashboard extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'crypto', 'content' => $this->translate('Crypto') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Crypto/Dashboard.twig');
  }

}
