<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers;

class Tags extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'tags', 'content' => $this->translate('Tags') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:EmailMarketing/Tags.twig');
  }

}
