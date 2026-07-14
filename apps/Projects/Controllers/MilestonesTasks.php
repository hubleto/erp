<?php

namespace Hubleto\App\Community\Projects\Controllers;

class MilestonesTasks extends \Hubleto\Erp\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'content' => $this->translate('Assign task to milestone') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Projects/MilestonesTasks.twig');
  }

}
