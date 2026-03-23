<?php

namespace Hubleto\App\Community\EventFeedback\Controllers;

class Contacts extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'events-feedback', 'content' => $this->translate('EventFeedback') ],
      [ 'url' => 'events-feedback/contacts', 'content' => $this->translate('Contacts') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:EventFeedback/Contacts.twig');
  }

}
