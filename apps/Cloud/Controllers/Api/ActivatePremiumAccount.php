<?php

namespace HubletoApp\Community\Cloud\Controllers\Api;

class ActivatePremiumAccount extends \Hubleto\Framework\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $this->hubletoApp->activatePremiumAccount();
    $this->main->router->redirectTo('cloud');
  }

}
