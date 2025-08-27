<?php declare(strict_types=1);

namespace Hubleto\Erp\Cli\Agent;

class CommandHelp extends \Hubleto\Erp\Cli\Agent\Command
{
  public function run(): void
  {
    \Hubleto\Terminal::cyan((string) file_get_contents(__DIR__ . "/help.md"));
  }
}
