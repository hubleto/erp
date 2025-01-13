<?php

namespace HubletoApp\Upgrade\Controllers;

class Upgrade extends \HubletoMain\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    if ($this->main->params['simulate'] == 'up') {
      file_put_contents($this->main->config['accountDir'] . '/pro', '1');
      $this->main->router->redirectTo('');
    }

    $this->setView('@app/Upgrade/Views/Upgrade.twig');
  }

}