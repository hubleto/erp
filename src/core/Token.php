<?php

namespace HubletoMain\Core;

class Token extends \ADIOS\Models\Token
{
  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'login' => new \ADIOS\Core\Db\Column\Varchar($this, 'Login'),
    ]);
  }
}