<?php

namespace Hubleto\App\Community\EventRegistrations\Controllers;

class Contacts extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'events-registrations', 'content' => $this->translate('EventRegistrations') ],
      [ 'url' => 'events-registrations/contacts', 'content' => $this->translate('Contacts') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:EventRegistrations/Contacts.twig');
  }

}
