<?php declare(strict_types=1);

namespace HubletoMain;

class Hook
{

  public function __construct(public \Hubleto\Framework\Loader $main)
  {
  }

  public function run(string $event, array $args): void
  {
    // to be overriden
  }

}
