<?php declare(strict_types=1);

// wrapper for `php hubleto` command

namespace Hubleto\Erp\Cli\Agent;

class Command extends \Hubleto\Erp\Core
{

  public array $arguments = [];

  /**
   * [Description for run]
   *
   * @return void
   * 
   */
  public function run(): void
  {
    // to be implemented in sub-classes
  }

  /**
   * [Description for getArguments]
   *
   * @return array
   * 
   */
  public function getArguments(): array
  {
    return $this->arguments;
  }

  /**
   * [Description for setArguments]
   *
   * @param array $arguments
   * 
   * @return Command
   * 
   */
  public function setArguments(array $arguments): Command
  {
    $this->arguments = $arguments;
    return $this;
  }

  public function setTerminalOutput(mixed $output): Command
  {
    $this->terminal()->setOutput($output);
    return $this;
  }


}
