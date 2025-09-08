<?php

namespace Hubleto\App\Community\EventRegistrations\Controllers;

class Contacts extends \Hubleto\Erp\Controller
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
    $this->setView('@Hubleto:App:Community:EventRegistrations/Contacts.twig');
  }

}
