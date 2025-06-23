<?php

namespace HubletoApp\Community\CalendarActivities\Controllers;

class Contacts extends \HubletoMain\Core\Controllers\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'calendaractivities', 'content' => 'CalendarActivities' ],
      [ 'url' => 'calendaractivities/contacts', 'content' => 'Contacts' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:CalendarActivities/Contacts.twig');
  }

}