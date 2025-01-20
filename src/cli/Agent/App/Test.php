<?php

namespace HubletoMain\Cli\Agent\App;

class Test extends \HubletoMain\Cli\Agent\Command
{
  public function run()
  {
    $appClass = $this->arguments[3] ?? '';
    $test = $this->arguments[4] ?? '';

    $this->main->testMode = true;

    try {
      $appManager = new \HubletoMain\Core\AppManager($this->main);
      $appManager->setCli($this->cli);
      $appManager->testApp($appClass, $test);
      $this->cli->cyan("✓ {$appClass} passed successfully test '{$test}'.\n");
    } catch (\Throwable $e) {
      $this->cli->red("✕ {$appClass} test failed.\n");
      $this->cli->red($e->getMessage() . "\n");
      $this->cli->red($e->getTraceAsString() . "\n");
    }
  }
}