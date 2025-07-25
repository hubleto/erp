<?php

namespace HubletoMain\Cli\Agent\Code;

class ListTemplates extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    \Hubleto\Terminal::cyan("Templates for `code generate`:\n");
    \Hubleto\Terminal::cyan("  Model - generate model to specified app\n");
  }
}
