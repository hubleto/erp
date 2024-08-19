<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

class BusinessAccount extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'business_accounts';
  public string $table = 'business_accounts';
  public string $eloquentClass = Eloquent\BusinessAccount::class;
  public ?string $lookupSqlValue = "{%TABLE%}.id";

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "id_company" => [
        "type" => "lookup",
        "title" => "Company",
        "model" => "CeremonyCrmApp/Modules/Core/Customers/Models/Company",
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
      ],
      "vat_id" => [
        "type" => "varchar",
        "title" => "VAT ID",
      ],
      "company_id" => [
        "type" => "varchar",
        "title" => "Company ID",
      ],
      "tax_id" => [
        "type" => "varchar",
        "title" => "Tax ID",
      ],
    ]));
  }

  public function tableParams(array $params = []): array
  {
    $params = parent::tableParams();
    $params['title'] = 'Business Account';
    return $params;
  }

}
