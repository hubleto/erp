<?php

namespace HubletoApp\Community\Settings\Models;

class InvoiceProfile extends \ADIOS\Core\Model {
  public string $table = 'invoice_profiles';
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public string $eloquentClass = Eloquent\InvoiceProfile::class;

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "name" => [ "type" => "varchar", "title" => $this->translate("Name") ],
      "phone" => [ "type" => "varchar", "title" => $this->translate("Phone") ],
      "email" => [ "type" => "varchar", "title" => $this->translate("E-mail") ],
      "www" => [ "type" => "varchar", "title" => $this->translate("WWW") ],
      "company_id" => [ "type" => "varchar", "title" => $this->translate("Company ID") ],
      "tax_id" => [ "type" => "varchar", "title" => $this->translate("Tax ID") ],
      "vat_id" => [ "type" => "varchar", "title" => $this->translate("VAT ID") ],
      "streer_1" => [ "type" => "varchar", "title" => $this->translate("Street, line 1") ],
      "streer_2" => [ "type" => "varchar", "title" => $this->translate("Street, line 2") ],
      "zip" => [ "type" => "varchar", "title" => $this->translate("ZIP") ],
      "city" => [ "type" => "varchar", "title" => $this->translate("City") ],
      "country" => [ "type" => "varchar", "title" => $this->translate("Country") ],
      "bank_name" => [ "type" => "varchar", "title" => $this->translate("Bank name") ],
      "account_number" => [ "type" => "varchar", "title" => $this->translate("Account number") ],
      "account_iban" => [ "type" => "varchar", "title" => $this->translate("Account IBAN") ],
      "swift" => [ "type" => "varchar", "title" => $this->translate("SWIFT") ],
      "numbering_pattern" => [ "type" => "varchar", "title" => $this->translate("Numbering pattern") ],
    ]));
  }

  public function tableDescribe(array $description = []): array {
    $description = parent::tableDescribe($description);

    $description['ui']['addButtonText'] = "Add invoice profile";

    unset($description['columns']['phone']);
    unset($description['columns']['email']);
    unset($description['columns']['www']);
    unset($description['columns']['tax_id']);
    unset($description['columns']['vat_id']);
    unset($description['columns']['street_1']);
    unset($description['columns']['street_2']);
    unset($description['columns']['zip']);
    unset($description['columns']['city']);
    unset($description['columns']['country']);
    unset($description['columns']['bank_name']);
    unset($description['columns']['account_number']);
    unset($description['columns']['swift']);

    return $description;
  }

  public function formDescribe(array $description = []): array {
    $description = parent::formDescribe($description);

    $description['ui'] = [
      'title' => 'Invoice profile',
      'subTitle' => '',
    ];

    return $description;
  }

}