<?php declare(strict_types=1);

namespace Hubleto\Erp\Emails;

class EmailWrapper
{
  public \Hubleto\Erp\Emails\EmailProvider $emailProvider;

  public function __construct(public \Hubleto\Framework\Loader $main)
  {
  }

}
