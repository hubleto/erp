<?php

namespace HubletoApp\Upgrade\Controllers;

class Upgrade extends \HubletoCore\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    if ($this->app->params['simulate'] == 'up') {
      file_put_contents($this->app->config['accountDir'] . '/pro', '1');
      $this->app->router->redirectTo('');
    }

    $this->setView('@app/Upgrade/Views/Upgrade.twig');
  }

}