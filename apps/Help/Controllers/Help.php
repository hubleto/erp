<?php

namespace HubletoApp\Community\Help\Controllers;

class Help extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'help', 'content' => $this->translate('Help') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Help/Help.twig');
  }

}
