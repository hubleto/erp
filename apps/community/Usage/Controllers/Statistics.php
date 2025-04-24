<?php

namespace HubletoApp\Community\Usage\Controllers;

class Statistics extends \HubletoMain\Core\Controllers\Controller {

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'usage', 'content' => $this->translate('Usage') ],
      [ 'url' => '', 'content' => $this->translate('Statistics') ],
    ]);
  }

  public function prepareView(): void {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Usage/Statistics.twig');
  }
}