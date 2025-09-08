<?php

namespace Hubleto\App\Community\Campaigns\Controllers;

class Campaigns extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'campaigns', 'content' => $this->translate('Campaigns') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:Campaigns/Campaigns.twig');
  }

}
