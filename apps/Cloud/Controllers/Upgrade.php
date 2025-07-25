<?php

namespace HubletoApp\Community\Cloud\Controllers;

class Upgrade extends \HubletoMain\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    if ($this->main->urlParamAsString('simulate') == 'up') {
      file_put_contents($this->main->config->getAsString('rootFolder') . '/pro', '1');
      $this->main->router->redirectTo('');
    }

    $currentCredit = $this->hubletoApp->recalculateCredit();
    $this->viewParams['currentCredit'] = $currentCredit;

    $this->setView('@HubletoApp:Community:Cloud/Upgrade.twig');
  }

}
