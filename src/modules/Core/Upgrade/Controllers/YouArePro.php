<?php

namespace CeremonyCrmMod\Core\Upgrade\Controllers;

class YouArePro extends \CeremonyCrmApp\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    if ($this->app->params['simulate'] == 'down') {
      @unlink($this->app->config['accountDir'] . '/pro');
      $this->app->router->redirectTo('');
    }

    $this->setView('@mod/Core/Upgrade/Views/YouArePro.twig');
  }

}