<?php

namespace HubletoApp\Community\Settings\Controllers;

class ContactTypes extends \HubletoMain\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'contact-types', 'content' => $this->translate('Contact Types') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/community/Settings/Views/ContactTypes.twig');
  }

}