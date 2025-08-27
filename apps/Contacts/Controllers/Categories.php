<?php

namespace HubletoApp\Community\Contacts\Controllers;

class Categories extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'contacts', 'content' => $this->translate('Contacts') ],
      [ 'url' => 'categories', 'content' => $this->translate('Categories') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Contacts/Categories.twig');
  }

}
