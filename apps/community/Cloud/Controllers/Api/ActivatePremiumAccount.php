<?php

namespace HubletoApp\Community\Cloud\Controllers\Api;

class ActivatePremiumAccount extends \HubletoMain\Core\Controllers\Controller {

  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): ?array
  {
    $this->hubletoApp->saveConfig('premiumAccountSince', date('Y-m-d H:i:s'));
    $this->hubletoApp->saveConfig('subscriptionRenewalActive', '1');
    $this->hubletoApp->saveConfig('subscriptionActiveUntil', date('Y-m-d H:i:s', strtotime('+1 month')));
    $this->hubletoApp->saveConfig('freeTrialPeriodUntil', date('Y-m-d H:i:s', strtotime('+1 month')));
    $this->main->router->redirectTo('cloud');
  }

}