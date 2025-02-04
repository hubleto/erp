<?php

namespace HubletoMain\Cli\Agent\App;

class Test extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    $appClass = (string) ($this->arguments[3] ?? '');
    $test = (string) ($this->arguments[4] ?? '');

    $appManager = new \HubletoMain\Core\AppManager($this->main);
    $appManager->setCli($this->cli);

    if (empty($appClass)) {
      $this->cli->white("Usage:\n");
      $this->cli->white("  Run a specific test: php hubleto app test <appClass> <testName>\n");
      $this->cli->white("  Run all tests in app: php hubleto app test <appClass>\n");
      return;
    }
    
    if (empty($test)) {
      $app = $appManager->createAppInstance($appClass);
      $tests = $app->getAllTests();
    } else {
      $tests = [$test];
    }

    $this->main->testMode = true;

    try {

      foreach ($tests as $test) {
        $appManager->testApp($appClass, $test);
        $this->cli->cyan("✓ {$appClass} passed successfully test '{$test}'.\n");
      }

    } catch (\Throwable $e) {
      $this->cli->red("✕ {$appClass} test '{$test}' failed.\n");
      $this->cli->red($e->getMessage() . "\n");
      $this->cli->red($e->getTraceAsString() . "\n");
    }
  }
}