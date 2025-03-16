<?php

namespace HubletoMain\Core;

class ControllerDesktop extends \ADIOS\Core\Controller {

  public string $translationContext = 'ADIOS\\Core\\Loader::Controllers\\Desktop';

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@hubleto/Desktop.twig');
  }
}