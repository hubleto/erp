<?php

namespace HubletoApp\Community\Contacts\Controllers;

class ContactCategories extends \HubletoMain\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'contact-categories', 'content' => $this->translate('Contact Categories') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Contacts/ContactCategories.twig');
  }

}