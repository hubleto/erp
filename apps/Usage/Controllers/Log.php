<?php

namespace Hubleto\App\Community\Usage\Controllers;

class Log extends \Hubleto\Erp\Controller
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
    $this->setView('@Hubleto:App:Community:Usage/Log.twig');
  }
}
