<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Controllers\Company;

use Illuminate\Database\Eloquent\Builder;

class TableCompanies extends \ADIOS\Controllers\Components\Table {

public function prepareLoadRecordQuery(): Builder
{
  $query = parent::prepareLoadRecordQuery();

  $query = $query
  ->selectRaw(" tax_id, vat_id, company_id")
  ->join("business_accounts", "business_accounts.id_company", "companies.id")
  ;

   //var_dump($this->params); exit;
  /* if ($this->params["idAccount"]) {
    $query = $query->where("join_id_company.id_account");
  } */

  return $query;
}

}