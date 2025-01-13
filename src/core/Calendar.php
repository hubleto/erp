<?php

namespace HubletoMain\Core;

class Calendar {

  public \HubletoMain $main;

  public function __construct(\HubletoMain $main) {
    $this->main = $main;
  }

  public function loadEvents(array $params = []): array
  {
    return [];
  }

}