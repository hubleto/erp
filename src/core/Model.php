<?php

namespace HubletoMain\Core;

class Model extends \ADIOS\Core\Model {
  public \HubletoMain $main;

  public array $conversionRelations = [];

  function __construct(\HubletoMain $main)
  {
    $this->main = $main;
    parent::__construct($main);
  }

}