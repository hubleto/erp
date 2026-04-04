<?php

namespace Hubleto\App\Community\Documents\Controllers;

class DocumentVersions extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'documents/versions', 'content' => $this->translate('Versions') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Documents/DocumentVersions.twig');
  }

}
