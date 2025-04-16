<?php

namespace HubletoApp\Community\Desktop\Controllers;

class Home extends \HubletoMain\Core\Controllers\Controller {

  public function init(): void
  {
    switch ($this->main->auth->getUserLanguage()) {
      case 'sk':
        $this->main->apps->community('Help')->addHotTip('sk/zakaznici/vytvorenie-noveho-kontaktu', 'Pridať nový kontakt');
      break;
    }
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Desktop/Home.twig');
  }

}