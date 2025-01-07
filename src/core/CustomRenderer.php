<?php

namespace HubletoCore\Core;

class CustomRenderer {
  public function render(string $view, array $params): string {
    return print_r($view, true);
  }
}