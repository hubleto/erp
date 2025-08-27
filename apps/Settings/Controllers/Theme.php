<?php

namespace Hubleto\App\Community\Settings\Controllers;

class Theme extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'theme', 'content' => $this->translate('Theme') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $themes = ['default', 'grayscale', 'pink'];

    $set = $this->getRouter()->urlParamAsString('set');
    if (!empty($set) && in_array($set, $themes)) {
      $this->getConfig()->save('uiTheme', $set);
      $this->getRouter()->redirectTo($this->getRouter()->getRoute());
    }

    $this->viewParams['themes'] = $themes;

    $this->setView('@Hubleto:App:Community:Settings/Theme.twig');
  }

}
