<?php

namespace HubletoApp\Community\Deals\Controllers;

class LostReasons extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => '', 'content' => $this->translate('Deal Lost Reasons') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Deals/LostReasons.twig');
  }

}
