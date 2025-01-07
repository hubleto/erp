<?php

namespace HubletoApp\Upgrade\Controllers;

class YouArePro extends \HubletoCore\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    if ($this->app->params['simulate'] == 'down') {
      @unlink($this->app->config['accountDir'] . '/pro');
      $this->app->router->redirectTo('');
    }

    $this->setView('@app/Upgrade/Views/YouArePro.twig');
  }

}