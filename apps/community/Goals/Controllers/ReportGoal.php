<?php

namespace HubletoApp\Community\Goals\Controllers;

class ReportGoal extends \HubletoMain\Core\Controller {

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'goals', 'content' => $this->translate('Goals') ],
      [ 'url' => '', 'content' => $this->translate('Report') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Goals/ReportGoal.twig');
  }

}