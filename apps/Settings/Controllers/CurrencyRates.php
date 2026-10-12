<?php

namespace Hubleto\App\Community\Settings\Controllers;

class CurrencyRates extends \Hubleto\Erp\Controller
{
  public string $permission = 'Hubleto/App/Custom/Settings/Loader::Controllers/CurrencyRates';

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => '', 'content' => $this->translate('Currency rates') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Custom:Settings/CurrencyRates.twig');
  }
}
