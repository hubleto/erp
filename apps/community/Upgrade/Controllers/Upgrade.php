<?php

namespace HubletoApp\Community\Upgrade\Controllers;

class Upgrade extends \HubletoMain\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    if ($this->main->urlParamAsString('simulate') == 'up') {
      file_put_contents($this->main->configAsString('accountDir') . '/pro', '1');
      $this->main->router->redirectTo('');
    }

    $this->setView('@HubletoApp:Community:Upgrade/Upgrade.twig');
  }

}