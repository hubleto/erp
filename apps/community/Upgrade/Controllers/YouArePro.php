<?php

namespace HubletoApp\Community\Upgrade\Controllers;

class YouArePro extends \HubletoMain\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    if ($this->main->urlParamAsString('simulate') == 'down') {
      @unlink($this->main->config->getAsString('accountDir') . '/pro');
      $this->main->router->redirectTo('');
    }

    $this->setView('@HubletoApp:Community:Upgrade/YouArePro.twig');
  }

}