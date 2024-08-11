<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Controllers;

use CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent\Person;
use Illuminate\Database\Eloquent\Builder;

class PersonsTable extends \ADIOS\Controllers\Components\Table {

public function prepareLoadRecordQuery(): Builder
{
  //$query = parent::prepareLoadRecordQuery();

  $mPersons = new Person();

  $query = $mPersons
    ->with(["CONTACTS" => function ($query) {
      $query->select("value as virt_contact")->first();
    }])
    ->with(["ADRESSES" => function ($query) {
      $query->select("concat(street, ', ', city) as virt_address")->first();
    }])
  ;

  var_dump($query->toSql()); exit;

  return $query;
}

}