<?php declare(strict_types=1);

// wrapper for `php hubleto` command

namespace HubletoMain\Cli\Agent;

class Command extends \HubletoMain\CoreClass
{

  public function __construct(public \HubletoMain\Loader $main, public array $arguments)
  {
  }

  public function run(): void
  {
    // to be implemented in sub-classes
  }

}
