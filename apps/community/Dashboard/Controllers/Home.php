<?php

namespace HubletoApp\Community\Dashboard\Controllers;

class Home extends \HubletoMain\Core\Controller {

  public function init(): void
  {
    switch ($this->main->auth->getUserLanguage()) {
      case 'sk':
        $this->main->help->addHotTip('sk/zakaznici/vytvorenie-noveho-kontaktu', 'Pridať nový kontakt');
      break;
    }
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Dashboard/Home.twig');
  }

}