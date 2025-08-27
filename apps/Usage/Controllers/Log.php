<?php

namespace HubletoApp\Community\Usage\Controllers;

class Log extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'usage', 'content' => $this->translate('Usage') ],
      [ 'url' => '', 'content' => $this->translate('Log') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Usage/Log.twig');
  }
}
