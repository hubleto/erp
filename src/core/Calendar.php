<?php

namespace HubletoMain\Core;

class Calendar {

  public \HubletoMain $app;

  public function __construct(\HubletoMain $app) {
    $this->app = $app;
  }

  public function loadEvents(array $params = []): array
  {
    return [];
  }

}