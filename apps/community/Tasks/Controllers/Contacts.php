<?php

namespace HubletoApp\Community\Tasks\Controllers;

class Contacts extends \HubletoMain\Core\Controllers\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'tasks', 'content' => 'Tasks' ],
      [ 'url' => 'tasks/contacts', 'content' => 'Contacts' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Tasks/Contacts.twig');
  }

}