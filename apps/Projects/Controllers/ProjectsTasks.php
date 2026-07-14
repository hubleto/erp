<?php

namespace Hubleto\App\Community\Projects\Controllers;

class ProjectsTasks extends \Hubleto\Erp\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'content' => $this->translate('Assign task to project') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Projects/ProjectsTasks.twig');
  }

}
