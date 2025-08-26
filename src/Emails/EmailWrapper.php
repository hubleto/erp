<?php declare(strict_types=1);

namespace HubletoMain\Emails;

class EmailWrapper
{
  public \HubletoMain\Emails\EmailProvider $emailProvider;

  public function __construct(public \Hubleto\Framework\Loader $main)
  {
  }

}
