<?php

namespace Hubleto\App\Community\Documents\Controllers;

class ReviewResults extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'documents/review-results', 'content' => $this->translate('Review results') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Documents/ReviewResults.twig');
  }

}
