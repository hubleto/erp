<?php

namespace Hubleto\Framework\Controllers;

use Hubleto\Framework\Controllers\Controller;

class ControllerNotFound extends Controller
{
  public bool $requiresUserAuthentication = false;
  public bool $hideDefaultDesktop = true;
  public string $translationContext = 'ADIOS\\Core\\Loader::Controllers\\NotFound';

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@hubleto/NotFound.twig');
  }

}
