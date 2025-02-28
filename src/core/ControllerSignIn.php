<?php

namespace HubletoMain\Core;

class ControllerSignIn extends \ADIOS\Core\Controller {

  public bool $requiresUserAuthentication = false;
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@hubleto/SignIn.twig');
  }

}