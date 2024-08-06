<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models;

class User extends \ADIOS\Models\User {
  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'email' => [
        'type' => 'varchar',
        'title' => $this->translate('Email'),
        'show' => true,
      ],
    ]));
  }
}