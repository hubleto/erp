<?php

namespace Hubleto\Erp;

/**
 * Methods to support locale in Hubleto project.
 */
class Locale extends \Hubleto\Framework\Locale
{

  /**
   * [Description for getTimezone]
   *
   * @return string
   * 
   */
  public function getTimezone(): string
  {
    $defaultTimezone = $this->config()->getAsString('locale/timezone', 'Europe/London');
    $userTimezone = $this->authProvider()->getUser()['timezone'];

    return (empty($userTimezone) ? $defaultTimezone : $userTimezone);
  }

}