<?php

namespace HubletoMain\Core\Models;

use ADIOS\Core\Exceptions\GeneralException;

class Token extends \ADIOS\Models\Token
{

  /**
   * @var \Illuminate\Database\Eloquent\Model
   */
  public object $record;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'login' => new \ADIOS\Core\Db\Column\Varchar($this, 'Login'),
    ]);
  }

  public function install(): void
  {
    parent::install();
    try {
      $this->registerTokenType('reset-password');
    } catch (GeneralException $e) {
      // no problem...
    }
  }
}
