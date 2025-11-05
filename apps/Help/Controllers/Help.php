<?php

namespace Hubleto\App\Community\Help\Controllers;

class Help extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      // [ 'url' => 'help', 'content' => $this->translate('Help') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Help/Help.twig');
  }

}
