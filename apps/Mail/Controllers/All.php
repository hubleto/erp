<?php

namespace HubletoApp\Community\Mail\Controllers;

class All extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'mail', 'content' => $this->translate('Mail') ],
      [ 'url' => 'all', 'content' => $this->translate('All') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->viewParams['title'] = 'All';
    $this->viewParams['folder'] = 'all';

    $this->setView('@HubletoApp:Community:Mail/ListFolder.twig');
  }

}
