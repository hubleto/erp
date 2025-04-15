<?php

namespace HubletoMain\Core;

class Calendar implements \ADIOS\Core\Testable {

  public \HubletoMain $main;

/**
 * Specifies what Activity Form component will be opened and what title should be used for a new button in the `FormActivitySelector.tsx` component
 * @var array{"title": string, "formComponent": string}
 * */
  public array $activitySelectorConfig = [
    "title" => "",
    "formComponent" => ""
  ];

  public function __construct(\HubletoMain $main) {
    $this->main = $main;
  }

  public function loadEvents(string $dateStart, string $dateEnd): array
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