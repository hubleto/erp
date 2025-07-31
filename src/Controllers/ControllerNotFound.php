<?php declare(strict_types=1);

namespace HubletoMain\Controllers;

use Hubleto\Framework\Controllers\Controller;

class ControllerNotFound extends \HubletoMain\Controller
{
  public bool $requiresUserAuthentication = false;
  public bool $hideDefaultDesktop = true;
  public string $translationContext = 'Hubleto\\Core\\Loader::Controllers\\NotFound';

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@hubleto-main/NotFound.twig');
  }

}
