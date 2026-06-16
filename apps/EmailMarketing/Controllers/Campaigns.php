<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers;

class Campaigns extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:EmailMarketing/Campaigns.twig');
  }

}
