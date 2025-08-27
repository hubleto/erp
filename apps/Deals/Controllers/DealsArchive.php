<?php

namespace HubletoApp\Community\Deals\Controllers;

class DealsArchive extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'deals', 'content' => $this->translate('Deals') ],
      [ 'url' => '', 'content' => $this->translate('Archive') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Deals/DealsArchive.twig');
  }
}
