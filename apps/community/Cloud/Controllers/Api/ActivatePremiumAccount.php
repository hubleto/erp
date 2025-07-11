<?php

namespace HubletoApp\Community\Cloud\Controllers\Api;

class ActivatePremiumAccount extends \HubletoMain\Core\Controllers\Controller {

  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;
  public bool $permittedForAllUsers = true;

  public function renderJson(): ?array
  {
    $this->hubletoApp->activatePremiumAccount();
    $this->main->router->redirectTo('cloud');
  }

}