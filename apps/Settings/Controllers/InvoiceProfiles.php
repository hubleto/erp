<?php

namespace Hubleto\App\Community\Settings\Controllers;

class InvoiceProfiles extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'invoice-profiles', 'content' => $this->translate('Invoice profiles') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Settings/InvoiceProfiles.twig');
  }

}
