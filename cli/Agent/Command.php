<?php

// wrapper for `php hubleto` command

namespace HubletoMain\Cli\Agent;

class Command
{
  public \HubletoMain\Loader $main;

  public array $arguments = [];

  public function __construct(\HubletoMain\Loader $main, array $arguments)
  {
    $this->main = $main;
    $this->arguments = $arguments;
  }

  public function run(): void
  {
    // to be implemented in sub-classes
  }

}
