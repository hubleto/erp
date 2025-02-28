<?php

namespace HubletoMain\Core;

class ControllerDesktop extends \ADIOS\Core\Controller {
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@hubleto/Desktop.twig');
  }
}