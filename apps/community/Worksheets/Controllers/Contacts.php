<?php

namespace HubletoApp\Community\Worksheets\Controllers;

class Contacts extends \HubletoMain\Core\Controllers\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'worksheets', 'content' => 'Worksheets' ],
      [ 'url' => 'worksheets/contacts', 'content' => 'Contacts' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Worksheets/Contacts.twig');
  }

}