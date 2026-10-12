<?php

namespace Hubleto\App\Community\Settings\Controllers;

class VatRates extends \Hubleto\Erp\Controller
{
  public string $permission = 'Hubleto/App/Custom/Settings/Loader::Controllers/VatRates';

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => '', 'content' => $this->translate('VAT rates') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Custom:Settings/VatRates.twig');
  }
}
