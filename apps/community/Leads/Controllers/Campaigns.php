<?php

namespace HubletoApp\Community\Leads\Controllers;

class Campaigns extends \HubletoMain\Core\Controllers\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'leads', 'content' => $this->translate('Leads') ],
      [ 'url' => 'campaigns', 'content' => $this->translate('Campaigns') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@HubletoApp:Community:Leads/Campaigns.twig');
  }

}