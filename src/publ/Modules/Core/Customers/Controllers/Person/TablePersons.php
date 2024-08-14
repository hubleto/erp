<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Controllers\Person;

use Illuminate\Database\Eloquent\Builder;

class TablePersons extends \ADIOS\Controllers\Components\Table {

public function prepareLoadRecordQuery(): Builder
{
  $query = parent::prepareLoadRecordQuery();

  $query = $query->selectRaw("
    (Select value from person_contacts where id_person = persons.id and type = 'number' LIMIT 1) virt_number,
    (Select value from person_contacts where id_person = persons.id and type = 'email' LIMIT 1) virt_email,
    (Select concat(street, ', ', city) from person_addresses where id_person = persons.id LIMIT 1) virt_address
  ")
  ;

   //var_dump($this->params); exit;
  /* if ($this->params["idAccount"]) {
    $query = $query->where("join_id_company.id_account");
  } */

  return $query;
}

}