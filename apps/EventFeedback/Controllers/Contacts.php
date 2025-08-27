<?php

namespace Hubleto\App\Community\EventFeedback\Controllers;

class Contacts extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'eventfeedback', 'content' => 'EventFeedback' ],
      [ 'url' => 'eventfeedback/contacts', 'content' => 'Contacts' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:EventFeedback/Contacts.twig');
  }

}
