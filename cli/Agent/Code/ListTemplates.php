<?php

namespace Hubleto\Erp\Cli\Agent\Code;

class ListTemplates extends \Hubleto\Erp\Cli\Agent\Command
{
  public function run(): void
  {
    $this->terminal()->cyan("Templates for `code generate`:\n");
    $this->terminal()->cyan("  Model - generate model to specified app\n");
  }
}
