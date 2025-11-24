<?php

namespace Hubleto\App\Community\Campaigns\Controllers;

class RecipientStatuses extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'campaign/recipients', 'content' => $this->translate('Recipients') ],
      [ 'url' => '', 'content' => $this->translate('Statuses') ]
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:Campaigns/RecipientStatuses.twig');
  }

}
