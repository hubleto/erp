<?php

namespace HubletoApp\Community\Premium\Controllers;

class PremiumActivated extends \HubletoMain\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    if ($this->main->urlParamAsString('simulate') == 'down') {
      @unlink($this->main->config->getAsString('accountDir') . '/pro');
      $this->main->router->redirectTo('');
    }

    $this->setView('@HubletoApp:Community:Premium/PremiumActivated.twig');
  }

}