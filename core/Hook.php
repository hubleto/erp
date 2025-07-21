<?php

namespace HubletoMain\Core;

class Hook
{
  public \HubletoMain $main;

  public function __construct(\HubletoMain $main)
  {
    $this->main = $main;
  }

  public function run(string $event, array $args): void
  {
    // to be overriden
  }

}
