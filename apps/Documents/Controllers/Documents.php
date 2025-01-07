<?php

namespace HubletoApp\Documents\Controllers;

class Documents extends \HubletoMain\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'documents', 'content' => $this->app->translate('Documents') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Documents/Views/Documents.twig');
  }

}