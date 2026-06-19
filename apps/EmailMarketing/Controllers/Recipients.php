<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers;

class Recipients extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'recipients', 'content' => $this->translate('Email recipients') ]
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:EmailMarketing/Recipients.twig');
  }

}
