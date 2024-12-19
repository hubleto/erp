<?php

namespace CeremonyCrmMod\Upgrade\Controllers;

class YouArePro extends \CeremonyCrmApp\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    if ($this->app->params['simulate'] == 'down') {
      @unlink($this->app->config['accountDir'] . '/pro');
      $this->app->router->redirectTo('');
    }

    $this->setView('@mod/Upgrade/Views/YouArePro.twig');
  }

}