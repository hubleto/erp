<?php

namespace HubletoApp\Community\Messages\Controllers;

class Messages extends \HubletoMain\Core\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'messages', 'content' => $this->translate('Messages') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@HubletoApp:Community:Messages/Messages.twig');
  }

}