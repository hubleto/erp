<?php

namespace HubletoMain\Cli\Agent\App;

class Test extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    $appClass = $this->arguments[3] ?? '';
    $test = $this->arguments[4] ?? '';

    if (empty($appClass) || empty($test)) {
      $this->cli->white("Usage: php hubleto app test <appClass> <testName>\n");
    } else {
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
}