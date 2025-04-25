<?php

namespace HubletoApp\Community\Premium\Controllers;

class Upgrade extends \HubletoMain\Core\Controllers\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    if ($this->main->urlParamAsString('simulate') == 'up') {
      file_put_contents($this->main->config->getAsString('accountDir') . '/pro', '1');
      $this->main->router->redirectTo('');
    }

    $currentCredit = $this->hubletoApp->recalculateCredit();
    $this->viewParams['currentCredit'] = $currentCredit;

    $this->setView('@HubletoApp:Community:Premium/Upgrade.twig');
  }

}