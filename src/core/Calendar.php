<?php

namespace HubletoMain\Core;

class Calendar implements \ADIOS\Core\Testable {

  public \HubletoMain $main;

  public function __construct(\HubletoMain $main) {
    $this->main = $main;
  }

  public function loadEvents(): array
  {
    return [];
  }

  public function assert(string $assertionName, bool $assertion): void
  {
    if ($this->main->testMode && !$assertion) {
      throw new \ADIOS\Core\Exceptions\TestAssertionFailedException('TEST FAILED: Assertion [' . $assertionName . '] not fulfilled in ' . get_parent_class($this));
    }
  }

}