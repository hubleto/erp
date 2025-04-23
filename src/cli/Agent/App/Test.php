<?php

namespace HubletoMain\Cli\Agent\App;

class Test extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = (string) ($this->arguments[3] ?? '');
    $test = (string) ($this->arguments[4] ?? '');

    $this->main->apps->setCli($this->cli);

    if (empty($appappNamespaceClass)) {
      $this->cli->white("Usage:\n");
      $this->cli->white("  Run a specific test: php hubleto app test <appNamespace> <testName>\n");
      $this->cli->white("  Run all tests in app: php hubleto app test <appNamespace>\n");
      return;
    }
    
    if (empty($test)) {
      $app = $this->main->apps->createAppInstance($appNamespace);
      $tests = $app->getAllTests();
    } else {
      $tests = [$test];
    }

    $this->main->testMode = true;

    try {

      foreach ($tests as $test) {
        $this->main->apps->testApp($appNamespace, $test);
        $this->cli->cyan("✓ {$appNamespace} passed successfully test '{$test}'.\n");
      }

    } catch (\Throwable $e) {
      $this->cli->red("✕ {$appNamespace} test '{$test}' failed.\n");
      $this->cli->red($e->getMessage() . "\n");
      $this->cli->red($e->getTraceAsString() . "\n");
    }
  }
}