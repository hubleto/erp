<?php

namespace HubletoApp\Community\Upgrade\Controllers;

class YouArePro extends \HubletoMain\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    if ($this->main->params['simulate'] == 'down') {
      @unlink($this->main->config['accountDir'] . '/pro');
      $this->main->router->redirectTo('');
    }

    $this->setView('@app/Upgrade/Views/YouArePro.twig');
  }

}