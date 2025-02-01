<?php

namespace HubletoApp\Community\Products\Models;

use HubletoApp\Community\Settings\Models\Country;

class Supplier extends \HubletoMain\Core\Model
{
  public string $table = 'product_suppliers';
  public string $eloquentClass = Eloquent\Supplier::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public function columnsLegacy(array $columns = []): array
  {
    return parent::columnsLegacy(array_merge($columns,[

      "title" => [
        "type" => "varchar",
        "title" => $this->translate("Title"),
        "required" => true,
      ],

      "address" => [
        "type" => "varchar",
        "title" => $this->translate("Address"),
        "required" => false,
      ],

      "city" => [
        "type" => "varchar",
        "title" => $this->translate("City"),
        "required" => false,
      ],

      "postal_code" => [
        "type" => "varchar",
        "title" => $this->translate("Postal Code"),
        "required" => false,
      ],

      "id_country" => [
        "type" => "lookup",
        "model" => Country::class,
        "title" => $this->translate("Country"),
        "required" => false,
      ],

      "contact_person" => [
        "type" => "varchar",
        "title" => $this->translate("Contact Person"),
        "required" => false,
      ],

      "phone_number" => [
        "type" => "varchar",
        "title" => $this->translate("Phone Number"),
        "required" => false,
      ],

      "email" => [
        "type" => "varchar",
        "title" => $this->translate("Supplier Email"),
        "required" => false,
      ],

      "order_email" => [
        "type" => "varchar",
        "title" => $this->translate("Order Email"),
        "required" => false,
      ],

      "tax_id" => [
        "type" => "varchar",
        "title" => $this->translate("Tax ID"),
        "required" => false,
      ],

      "company_id" => [
        "type" => "varchar",
        "title" => $this->translate("Company ID"),
        "required" => false,
      ],

      "vat_id" => [
        "type" => "varchar",
        "title" => $this->translate("VAT ID"),
        "required" => false,
      ],

      "payment_account" => [
        "type" => "varchar",
        "title" => $this->translate("Payment Account Number"),
        "required" => false,
      ],

    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe($description);

    if (is_array($description['ui'])) {
      $description['ui']['title'] = 'Product Suppliers';
      $description["ui"]["addButtonText"] = $this->translate("Add product supplier");
    }

    if (is_array($description['columns'])) {
      unset($description["columns"]["address"]);
      unset($description["columns"]["city"]);
      unset($description["columns"]["postal_code"]);
      unset($description["columns"]["id_country"]);
      unset($description["columns"]["tax_id"]);
      unset($description["columns"]["company_id"]);
      unset($description["columns"]["vat_id"]);
      unset($description["columns"]["payment_account"]);
    }

    return $description;
  }
}
