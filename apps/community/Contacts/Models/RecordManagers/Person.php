<?php

namespace HubletoApp\Community\Contacts\Models\RecordManagers;

use HubletoApp\Community\Customers\Models\RecordManagers\Customer;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends \HubletoMain\Core\RecordManager
{
  public $table = 'persons';

  /** @return BelongsTo<Customer, covariant Person> */
  public function CUSTOMER(): BelongsTo {
    return $this->belongsTo(Customer::class, 'id_customer');
  }

  /** @return HasMany<Contact, covariant Person> */
  public function CONTACTS(): HasMany {
     return $this->hasMany(Contact::class, 'id_person', 'id');
  }

  /** @return HasMany<PersonTag, covariant Person> */
  public function TAGS(): HasMany {
     return $this->hasMany(PersonTag::class, 'id_person', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $query = $query->selectRaw("
      (Select value from contacts where id_person = persons.id and type = 'number' LIMIT 1) virt_number,
      (Select value from contacts where id_person = persons.id and type = 'email' LIMIT 1) virt_email
    ");

    return $query;
  }

  public function addOrderByToQuery(mixed $query, array $orderBy): mixed
  {
    if (isset($orderBy['field']) && $orderBy['field'] == 'tags') {
      if (empty($this->joinManager["tags"])) {
        $this->joinManager["tags"]["order"] = true;
        $query
          ->addSelect("person_tags.name")
          ->join('cross_person_tags', 'cross_person_tags.id_person', '=', 'persons.id')
          ->join('person_tags', 'cross_person_tags.id_tag', '=', 'person_tags.id')
        ;
      }
      $query->orderBy('person_tags.name', $orderBy['direction']);

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
          ->addSelect("person_tags.name as personTag")
          ->join('cross_person_tags', 'cross_person_tags.id_person', '=', 'persons.id')
          ->join('person_tags', 'cross_person_tags.id_tag', '=', 'person_tags.id')
        ;
      }
      $query->orHaving('personTag', 'like', "%{$fulltextSearch}%");

      if (empty($this->joinManager["virt_contact"])) {
        $this->joinManager["virt_contact"]["fullText"] = true;
        $query
          ->addSelect("contacts.value")
          ->join('contacts', 'contacts.id_person', '=', 'persons.id')
        ;
      }
      $query->orHaving('contacts.value', 'like', "%{$fulltextSearch}%");

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
          ->addSelect("person_tags.name as personTag")
          ->join('cross_person_tags', 'cross_person_tags.id_person', '=', 'persons.id')
          ->join('person_tags', 'cross_person_tags.id_tag', '=', 'person_tags.id')
        ;
      }
      $query->having('personTag', 'like', "%{$columnSearch['tags']}%");
    }

    if (!empty($columnSearch) && !empty($columnSearch['virt_email'])) {
      if (empty($this->joinManager["virt_contact"])) {
        $this->joinManager["virt_contact"]["email"] = true;
        $query
          ->addSelect("contacts.value")
          ->join('contacts', 'contacts.id_person', '=', 'persons.id')
        ;
      }
      $query
        ->where("contacts.type", "email")
        ->orHaving('contacts.value', 'like', "%{$columnSearch['virt_email']}%")
      ;
    }

    if (!empty($columnSearch) && !empty($columnSearch['virt_number'])) {
      if (empty($this->joinManager["virt_contact"])) {
        $this->joinManager["virt_contact"]["number"] = true;
        $query
          ->addSelect("contacts.value")
          ->join('contacts', 'contacts.id_person', '=', 'persons.id')
        ;
      }
      $query
        ->where("contacts.type", "number")
        ->orHaving('contacts.value', 'like', "%{$columnSearch['virt_number']}%")
      ;
    }
    return $query;
  }
}
