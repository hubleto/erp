<?php

namespace HubletoCore\Core;

class Router extends \ADIOS\Core\Router {
  public function __construct(\ADIOS\Core\Loader $adios) {
    parent::__construct($adios);

    $this->httpGet([
      '/^api\/dictionary\/?$/' => Dictionary::class,
    ]);

  }
}
