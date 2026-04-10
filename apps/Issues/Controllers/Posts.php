<?php

namespace Hubleto\App\Community\Issues\Controllers;

class Posts extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'issues/posts', 'content' => $this->translate('Posts') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Issues/Posts.twig');
  }

}
