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

    $set = $this->router()->urlParamAsString('set');
    if (!empty($set) && in_array($set, $themes)) {
      $this->config()->save('uiTheme', $set);
      $this->router()->redirectTo($this->router()->getRoute());
    }

    $this->viewParams['themes'] = $themes;

    $this->setView('@Hubleto:App:Community:Settings/Theme.twig');
  }

}
