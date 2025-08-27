<?php

namespace HubletoApp\Community\Campaigns\Controllers;

class Campaigns extends \HubletoMain\Controller
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

    $this->setView('@HubletoApp:Community:Campaigns/Campaigns.twig');
  }

}
