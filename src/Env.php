<?php

namespace Hubleto\Erp;

/**
 * Storage for environment-specific configuration.
 */
class Env extends \Hubleto\Framework\Services\Env
{

  public string $accountUid = '';

  public function __construct()
  {
    parent::__construct();
    $this->accountUid = $this->config()->getAsString('accountUid');
  }

  /**
   * Checks whether this Hubleto installation can use premium features.
   *
   * @return boolean
   * 
   */
  public function isPremium(): bool
  {
    $isPremium = true;
    if ($this->appManager()->isAppEnabled('Hubleto/App/Community/Cloud')) {
      $cloudApp = $this->appManager()->getApp('Hubleto/App/Community/Cloud');
      $isPremium = $cloudApp->isPremium;
    }
    return $isPremium;
  }

}