<?php

namespace HubletoApp\Community\Projects\Controllers;

class Contacts extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'projects', 'content' => 'Projects' ],
      [ 'url' => 'projects/contacts', 'content' => 'Contacts' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Projects/Contacts.twig');
  }

}
