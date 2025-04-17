<?php

namespace HubletoApp\Community\Leads\Controllers;

class Leads extends \HubletoMain\Core\Controllers\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->translate('Leads') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Leads/Leads.twig');
  }
}