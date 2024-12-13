<?php

namespace CeremonyCrmMod\Core\Dashboard\Controllers;

class Home extends \CeremonyCrmApp\Core\Controller {

  public function init(): void
  {
    switch ($this->app->auth->user['language']) {
      case 'sk':
        $this->app->help->addHotTip('sk/zakaznici/vytvorenie-noveho-kontaktu', 'Pridať nový kontakt');
      break;
    }
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@mod/Core/Dashboard/Views/Home.twig');
  }
}