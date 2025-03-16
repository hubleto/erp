<?php

namespace HubletoApp\Community\Premium\Controllers;

class Premium extends \HubletoMain\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    if ($this->main->urlParamAsString('simulate') == 'up') {
      file_put_contents($this->main->config->getAsString('accountDir') . '/pro', '1');
      $this->main->router->redirectTo('premium');
    }

    if ($this->main->urlParamAsString('simulate') == 'down') {
      @unlink($this->main->config->getAsString('accountDir') . '/pro');
      $this->main->router->redirectTo('premium');
    }

    if ($this->app->isPro) {
      $this->setView('@HubletoApp:Community:Premium/PremiumActivated.twig');
    } else {
      $this->setView('@HubletoApp:Community:Premium/Upgrade.twig');
    }
  }

}