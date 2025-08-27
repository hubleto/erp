<?php

namespace HubletoApp\Community\EventRegistrations\Controllers;

class Contacts extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'eventregistrations', 'content' => 'EventRegistrations' ],
      [ 'url' => 'eventregistrations/contacts', 'content' => 'Contacts' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:EventRegistrations/Contacts.twig');
  }

}
