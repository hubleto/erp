<?php

namespace CeremonyCrmApp\Modules\Core\Dashboard\Controllers;

class Home extends \CeremonyCrmApp\Core\Controller {
  public string $translationContext = 'mod.core.customers.dashboard.home';

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
    $this->setView('@app/Modules/Core/Dashboard/Views/Home.twig');
  }
}