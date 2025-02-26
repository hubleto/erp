<?php

// wrapper for `php hubleto` command

namespace HubletoMain\Cli\Agent;

class Command
{

  public \HubletoMain\Cli\Agent\Loader $cli;
  public \HubletoMain $main;

  public array $arguments = [];

  public function __construct(\HubletoMain\Cli\Agent\Loader $cli, array $arguments)
  {
    $this->cli = $cli;
    $this->main = $cli->main;
    $this->arguments = $arguments;
  }

  public function run(): void
  {
    // to be implemented in sub-classes
  }

}