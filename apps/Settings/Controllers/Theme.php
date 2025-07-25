<?php

namespace HubletoApp\Community\Settings\Controllers;

class Theme extends \HubletoMain\Controller
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

    $set = $this->main->urlParamAsString('set');
    if (!empty($set) && in_array($set, $themes)) {
      $this->main->config->save('uiTheme', $set);
      $this->main->router->redirectTo($this->main->route);
    }

    $this->viewParams['themes'] = $themes;

    $this->setView('@HubletoApp:Community:Settings/Theme.twig');
  }

}
