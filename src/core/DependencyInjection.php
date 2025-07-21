<?php

namespace HubletoMain\Core;

class DependencyInjection extends \ADIOS\Core\DependencyInjection {

  use \HubletoMain\Core\Traits\MainTrait;

  public function __construct(\HubletoMain $main) {
    parent::__construct($main);
    $this->main = $main;

    // $this->setDependency('HubletoApp\\Community\\Settings\\Models\\User', \HubletoApp\Community\Settings\Models\User::class);
  }
  
}