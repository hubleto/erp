<?php

namespace HubletoApp\Community\Deals\Controllers;

class ReportGoal extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'sales', 'content' => $this->translate('Sales') ],
      [ 'url' => '', 'content' => $this->translate('Goals') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Deals/Goals.twig');
  }

}
