<?php

namespace HubletoApp\Community\Cloud\Controllers\Api;

class ActivatePremiumAccount extends \HubletoMain\Core\Controllers\ApiController
{

  public function renderJson(): ?array
  {
    $this->hubletoApp->activatePremiumAccount();
    $this->main->router->redirectTo('cloud');
  }

}