<?php

namespace HubletoApp\Community\Usage\Controllers;

class Home extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'usage', 'content' => $this->translate('Usage') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Usage/Home.twig');
  }
}
