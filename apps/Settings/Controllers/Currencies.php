<?php

namespace Hubleto\App\Community\Settings\Controllers;

class Currencies extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'currencies', 'content' => $this->translate('Currencies') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Settings/Currencies.twig');
  }

}
