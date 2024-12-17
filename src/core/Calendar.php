<?php

namespace CeremonyCrmApp\Core;

class Calendar {

  public \CeremonyCrmApp $app;

  public function __construct(\CeremonyCrmApp $app) {
    $this->app = $app;
  }

  public function loadEvents(array $params = []): array
  {
    return [];
  }

}