<?php

namespace HubletoApp\Community\Desktop\Controllers;

class Desktop extends \ADIOS\Core\Controller {

  // public string $translationContext = 'ADIOS\\Core\\Loader::Controllers\\Desktop';

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Desktop/Desktop.twig');
  }
}