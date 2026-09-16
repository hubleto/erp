<?php

namespace Hubleto\App\Community\Settings\Controllers;

class Dashboard extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      // [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $themes = ['default', 'classic', 'modern', 'grayscale', 'pink'];

    $setTheme = $this->router()->urlParamAsString('setTheme');
    if (!empty($setTheme) && in_array($setTheme, $themes)) {
      $this->config()->saveForUser('uiTheme', $setTheme);
      $this->router()->redirectTo($this->router()->getRoute());
    }

    $this->viewParams['themes'] = $themes;

    $this->viewParams['settings'] = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class)->getSettings();
    $this->setView('@Hubleto:App:Community:Settings/Dashboard.twig');
  }

}
