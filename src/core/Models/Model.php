<?php

namespace HubletoMain\Core\Models;

class Model extends \ADIOS\Core\Model {
  public \HubletoMain $main;

  public array $conversionRelations = [];
  public string $permission = '';
  public array $rolePermissions = []; // CRUD permissions; example: [ROLE_ADMINISTRATOR => [true, true, true, true]]

  function __construct(\HubletoMain $main)
  {
    $this->main = $main;

    $reflection = new \ReflectionClass($this);
    preg_match('/^(.*?)\\\Models\\\(.*?)$/', $reflection->getName(), $m);
    $this->translationContext = $m[1] . '\\Loader::Models\\' . $m[2];

    parent::__construct($main);

  }

}