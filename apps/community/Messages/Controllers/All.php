<?php

namespace HubletoApp\Community\Messages\Controllers;

class All extends \HubletoMain\Core\Controllers\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'messages', 'content' => $this->translate('Messages') ],
      [ 'url' => 'all', 'content' => $this->translate('All') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->viewParams['title'] = 'All';
    $this->viewParams['folder'] = 'all';

    $this->setView('@HubletoApp:Community:Messages/ListFolder.twig');
  }

}