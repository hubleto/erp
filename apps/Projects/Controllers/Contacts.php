<?php

namespace Hubleto\App\Community\Projects\Controllers;

class Contacts extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'projects', 'content' => $this->translate('Projects') ],
      [ 'url' => 'projects/contacts', 'content' => $this->translate('Contacts') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Projects/Contacts.twig');
  }

}
