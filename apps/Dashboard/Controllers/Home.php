<?php

namespace HubletoApp\Dashboard\Controllers;

class Home extends \HubletoCore\Core\Controller {

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
    $this->setView('@app/Dashboard/Views/Home.twig');
  }
}