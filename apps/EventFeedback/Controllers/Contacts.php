<?php

namespace HubletoApp\Community\EventFeedback\Controllers;

class Contacts extends \HubletoMain\Controller
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
    $this->setView('@HubletoApp:Community:EventFeedback/Contacts.twig');
  }

}
