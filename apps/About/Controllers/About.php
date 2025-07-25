<?php

namespace HubletoApp\Community\About\Controllers;

class About extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'about', 'content' => $this->translate('About') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:About/About.twig');
  }

}
