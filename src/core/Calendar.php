<?php

namespace HubletoCore\Core;

class Calendar {

  public \HubletoCore $app;

  public function __construct(\HubletoCore $app) {
    $this->app = $app;
  }

  public function loadEvents(array $params = []): array
  {
    return [];
  }

}