<?php

namespace HubletoMain\Core\Models;

class Model extends \ADIOS\Core\Model {
  public \HubletoMain $main;

  public array $conversionRelations = [];
  public string $permission = '';

  function __construct(\HubletoMain $main)
  {
    $this->main = $main;

    $reflection = new \ReflectionClass($this);
    preg_match('/^(.*?)\\\Models\\\(.*?)$/', $reflection->getName(), $m);
    $this->translationContext = $m[1] . '\\Loader::Models\\' . $m[2];
    $this->permission = $this->translationContext;

    parent::__construct($main);

  }

}