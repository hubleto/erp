<?php

namespace HubletoApp\Community\Mail\Controllers;

class Templates extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'mail', 'content' => $this->translate('Mail') ],
      [ 'url' => 'templates', 'content' => $this->translate('Templates') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->viewParams['title'] = 'Templates';
    $this->viewParams['folder'] = 'templates';

    $this->setView('@HubletoApp:Community:Mail/ListFolder.twig');
  }

}
