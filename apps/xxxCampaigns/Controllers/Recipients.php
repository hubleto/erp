<?php

namespace Hubleto\App\Community\Campaigns\Controllers;

class Recipients extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'recipients', 'content' => $this->translate('Recipients') ]
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:Campaigns/Recipients.twig');
  }

}
