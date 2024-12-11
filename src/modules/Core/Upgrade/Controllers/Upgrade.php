<?php

namespace CeremonyCrmMod\Core\Upgrade\Controllers;

class Upgrade extends \CeremonyCrmApp\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    if ($this->app->params['simulate'] == 'up') {
      file_put_contents($this->app->config['accountDir'] . '/pro', '1');
      $this->app->router->redirectTo('');
    }

    $this->setView('@mod/Core/Upgrade/Views/Upgrade.twig');
  }

}