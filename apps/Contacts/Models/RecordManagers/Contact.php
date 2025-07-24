<?php

namespace HubletoApp\Community\Contacts\Models\RecordManagers;

use HubletoApp\Community\Customers\Models\RecordManagers\Customer;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contact extends \Hubleto\Framework\RecordManager
{
  public $table = 'contacts';

  /** @return BelongsTo<Customer, covariant Contact> */
  public function CUSTOMER(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'id_customer');
  }

  /** @return HasMany<Contact, covariant Contact> */
  public function VALUES(): HasMany
  {
    return $this->hasMany(Value::class, 'id_contact', 'id');
  }

  /** @return HasMany<ContactTag, covariant Contact> */
  public function TAGS(): HasMany
  {
    return $this->hasMany(ContactTag::class, 'id_contact', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $query = $query->orderBy('is_primary', 'desc');

    $main = \HubletoMain\Loader::getGlobalApp();

    if ($main->urlParamAsInteger("idCustomer") > 0) {
      $query = $query->where($this->table . '.id_customer', $main->urlParamAsInteger("idCustomer"));
    }

    $query = $query->selectRaw("
      (Select value from contact_values where id_contact = contacts.id and type = 'number' LIMIT 1) virt_number,
      (Select value from contact_values where id_contact = contacts.id and type = 'email' LIMIT 1) virt_email
    ");

    return $query;
  }

  public function addOrderByToQuery(mixed $query, array $orderBy): mixed
  {
    if (isset($orderBy['field']) && $orderBy['field'] == 'tags') {
      if (empty($this->joinManager["tags"])) {
        $this->joinManager["tags"]["order"] = true;
        $query
          ->addSelect("contact_tags.name")
          ->leftJoin('contact_contact_tags', 'contact_contact_tags.id_contact', '=', 'contacts.id')
          ->leftJoin('contact_tags', 'contact_contact_tags.id_tag', '=', 'contact_tags.id')
        ;
      }
      $query->orderBy('contact_tags.name', $orderBy['direction']);

      return $query;
    } else {
      return parent::addOrderByToQuery($query, $orderBy);
    }
  }

  public function addFulltextSearchToQuery(mixed $query, string $fulltextSearch): mixed
  {
    if (!empty($fulltextSearch)) {
      $query = parent::addFulltextSearchToQuery($query, $fulltextSearch);

      if (empty($this->joinManager["tags"])) {
        $this->joinManager["tags"]["fullText"] = true;
        $query
          ->addSelect("contact_tags.name as contactTag")
          ->leftJoin('contact_contact_tags', 'contact_contact_tags.id_contact', '=', 'contacts.id')
          ->leftJoin('contact_tags', 'contact_contact_tags.id_tag', '=', 'contact_tags.id')
        ;
      }
      $query->orHaving('contactTag', 'like', "%{$fulltextSearch}%");

      if (empty($this->joinManager["virt_contact"])) {
        $this->joinManager["virt_contact"]["fullText"] = true;
        $query
          ->addSelect("contact_values.value")
          ->leftJoin('contact_values', 'contact_values.id_contact', '=', 'contacts.id')
        ;
      }
      $query
        ->orHaving('contact_values.value', 'like', "%{$fulltextSearch}%")
        ->groupBy("contacts.id")
      ;
    }
    return $query;
  }

  public function addColumnSearchToQuery(mixed $query, array $columnSearch): mixed
  {
    $query = parent::addColumnSearchToQuery($query, $columnSearch);

    if (!empty($columnSearch) && !empty($columnSearch['tags'])) {
      if (empty($this->joinManager["tags"])) {
        $this->joinManager["tags"]["column"] = true;
        $query
          ->addSelect("contact_tags.name as contactTag")
          ->leftJoin('contact_contact_tags', 'contact_contact_tags.id_contact', '=', 'contacts.id')
          ->leftJoin('contact_tags', 'contact_contact_tags.id_tag', '=', 'contact_tags.id')
        ;
      }
      $query->having('contactTag', 'like', "%{$columnSearch['tags']}%");
    }

    if (!empty($columnSearch) && !empty($columnSearch['virt_email'])) {
      if (empty($this->joinManager["virt_contact"])) {
        $this->joinManager["virt_contact"]["email"] = true;
        $query
          ->addSelect("contact_values.value")
          ->leftJoin('contact_values', 'contact_values.id_contact', '=', 'contacts.id')
        ;
      }
      $query
        ->where("contact_values.type", "email")
        ->having('contact_values.value', 'like', "%{$columnSearch['virt_email']}%")
      ;
    }

    if (!empty($columnSearch) && !empty($columnSearch['virt_number'])) {
      if (empty($this->joinManager["virt_contact"])) {
        $this->joinManager["virt_contact"]["number"] = true;
        $query
          ->addSelect("contact_values.value")
          ->leftJoin('contact_values', 'contact_values.id_contact', '=', 'contacts.id')
        ;
      }
      $query
        ->where("contact_values.type", "number")
        ->having('contact_values.value', 'like', "%{$columnSearch['virt_number']}%")
      ;
    }
    return $query;
  }

  public function prepareLookupQuery(string $search): mixed
  {
    $main = \HubletoMain\Loader::getGlobalApp();
    $idCustomer = $main->urlParamAsInteger('idCustomer');

    $query = parent::prepareLookupQuery($search);
    if ($idCustomer > 0) {
      $query->where($this->table . '.id_customer', $idCustomer);
    }

    return $query;
  }

  public function prepareLookupData(array $dataRaw): array
  {
    $data = parent::prepareLookupData($dataRaw);

    foreach ($dataRaw as $key => $value) {
      $data[$key]['_URL_DETAILS'] = 'contacts/' . $value['id'];
    }

    return $data;
  }

}
