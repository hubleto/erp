<?php

namespace Hubleto\App\Community\Projects\Controllers;

class Milestones extends \Hubleto\Erp\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'content' => $this->translate('Milestones') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Projects/Milestones.twig');
  }

}
