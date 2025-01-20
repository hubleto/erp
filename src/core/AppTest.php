<?php

namespace HubletoMain\Core;

class AppTest {
  public \HubletoMain $main;
  public \HubletoMain\Cli\Agent\Loader $cli;
  public \HubletoMain\Core\App $app;

  public function __construct(\HubletoMain\Core\App $app, \HubletoMain\Cli\Agent\Loader $cli)
  {
    $this->cli = $cli;
    $this->app = $app;
    $this->main = $app->main;
  }

  public function run(): void
  {
    // Throw exception if test fails
  }
}