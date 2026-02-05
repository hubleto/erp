<?php

namespace Hubleto\Erp;

/**
 * Shortcut to access all services used in the Hubleto project.
 */
class Core extends \Hubleto\Framework\Core
{

  /**
   * Shortcut for the email provider service.
   *
   * @return EmailProvider
   * 
   */
  public function emailProvider(): Interfaces\EmailProviderInterface
  {
    return $this->getService(EmailProvider::class);
  }
}