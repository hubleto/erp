<?php

namespace HubletoMain\Cli\Agent\App;

class Test extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = (string) ($this->arguments[3] ?? '');
    $test = (string) ($this->arguments[4] ?? '');

    if (empty($appappNamespaceClass)) {
      \Hubleto\Terminal::white("Usage:\n");
      \Hubleto\Terminal::white("  Run a specific test: php hubleto app test <appNamespace> <testName>\n");
      \Hubleto\Terminal::white("  Run all tests in app: php hubleto app test <appNamespace>\n");
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
        \Hubleto\Terminal::cyan("✓ {$appNamespace} passed successfully test '{$test}'.\n");
      }

    } catch (\Throwable $e) {
      \Hubleto\Terminal::red("✕ {$appNamespace} test '{$test}' failed.\n");
      \Hubleto\Terminal::red($e->getMessage() . "\n");
      \Hubleto\Terminal::red($e->getTraceAsString() . "\n");
    }
  }
}
