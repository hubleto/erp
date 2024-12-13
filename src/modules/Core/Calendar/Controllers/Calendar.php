<?php

namespace CeremonyCrmMod\Core\Calendar\Controllers;

class Calendar extends \CeremonyCrmApp\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'calendar', 'content' => $this->translate('Calendar') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@mod/Core/Calendar/Views/Calendar.twig');
  }

}