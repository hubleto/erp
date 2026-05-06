<?php

namespace Hubleto\App\Community\Documents\Controllers;

class Reviews extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'documents/reviews', 'content' => $this->translate('Reviews') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Documents/Reviews.twig');
  }

}
