<?php

namespace Hubleto\App\Community\BkJournal\Controllers;

class Entries extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'journal/entries', 'content' => $this->translate('Journal Entries') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Journal/Entries.twig');
  }

}
