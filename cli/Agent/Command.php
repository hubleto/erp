<?php declare(strict_types=1);

// wrapper for `php hubleto` command

namespace HubletoMain\Cli\Agent;

class Command extends \Hubleto\Framework\CoreClass
{

  public array $arguments = [];

  public function run(): void
  {
    // to be implemented in sub-classes
  }

  public function getArguments(): array
  {
    return $this->arguments;
  }

  public function setArguments(array $arguments): Command
  {
    $this->arguments = $arguments;
    return $this;
  }

}
