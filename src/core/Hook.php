<?php

namespace HubletoMain\Core;

class Hook {

  public \HubletoMain $main;
  public \HubletoMain\Cli\Agent\Loader $cli;

  public function __construct(\HubletoMain $main, \HubletoMain\Cli\Agent\Loader $cli)
  {
    $this->main = $main;
    $this->cli = $cli;
  }

}