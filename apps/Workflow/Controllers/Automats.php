<?php

namespace Hubleto\App\Community\Workflow\Controllers;

class Automats extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'automats', 'content' => $this->translate('Automats') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Workflow/Automats.twig');
  }
}
