<?php

namespace HubletoApp\Community\Customers\Models\RecordManagers;

use HubletoApp\Community\Billing\Models\RecordManagers\BillingAccount;
use HubletoApp\Community\Contacts\Models\RecordManagers\Contact;
use HubletoApp\Community\Customers\Models\RecordManagers\CustomerDocument;
use HubletoApp\Community\Settings\Models\RecordManagers\Country;
use HubletoApp\Community\Settings\Models\RecordManagers\User;
use HubletoApp\Community\Deals\Models\RecordManagers\Deal;
use HubletoApp\Community\Leads\Models\RecordManagers\Lead;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Customer extends \Hubleto\Framework\RecordManager
{
  public $table = 'customers';

  /** @return HasMany<Contact, covariant Customer> */
  public function CONTACTS(): HasMany
  {
    return $this->hasMany(Contact::class, 'id_customer');
  }

  /** @return HasOne<Country, covariant Customer> */
  public function COUNTRY(): HasOne
  {
    return $this->hasOne(Country::class, 'id', 'id_country');
  }

  /** @return HasMany<CustomerActivity, covariant Customer> */
  public function ACTIVITIES(): HasMany
  {
    return $this->hasMany(CustomerActivity::class, 'id_customer', 'id');
  }

  /** @return HasMany<CustomerDocument, covariant Customer> */
  public function DOCUMENTS(): HasMany
  {
    return $this->hasMany(CustomerDocument::class, 'id_lookup', 'id');
  }

  /** @return HasMany<CustomerTag, covariant Customer> */
  public function TAGS(): HasMany
  {
    return $this->hasMany(CustomerTag::class, 'id_customer', 'id');
  }

  /** @return HasMany<Lead, covariant Customer> */
  public function LEADS(): HasMany
  {
    return $this->hasMany(Lead::class, 'id_customer', 'id');
  }

  /** @return HasMany<Deal, covariant Customer> */
  public function DEALS(): HasMany
  {
    return $this->hasMany(Deal::class, 'id_customer', 'id');
  }

  /** @return BelongsTo<User, covariant Customer> */
  public function OWNER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_owner', 'id');
  }

  /** @return BelongsTo<User, covariant Customer> */
  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \HubletoMain\Loader::getGlobalApp();

    $defaultFilters = $main->urlParamAsArray("defaultFilters");
    if (isset($defaultFilters["fArchive"])) {
      if ($defaultFilters["fArchive"] == 1) {
        $query = $query->where("customers.is_active", false);
      } else {
        $query = $query->where("customers.is_active", true);
      }
    }

    return $query;
  }

  public function addOrderByToQuery(mixed $query, array $orderBy): mixed
  {
    if (isset($orderBy['field']) && $orderBy['field'] == 'tags') {
      if (empty($this->joinManager["tags"])) {
        $this->joinManager["tags"]["order"] = true;
        $query
          ->addSelect("customer_tags.name")
          ->leftJoin('cross_customer_tags', 'cross_customer_tags.id_customer', '=', 'customers.id')
          ->leftJoin('customer_tags', 'cross_customer_tags.id_tag', '=', 'customer_tags.id')
        ;
      }
      $query->orderBy('customer_tags.name', $orderBy['direction']);

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
          ->addSelect("customer_tags.name as customerTag")
          ->leftJoin('cross_customer_tags', 'cross_customer_tags.id_customer', '=', 'customers.id')
          ->leftJoin('customer_tags', 'cross_customer_tags.id_tag', '=', 'customer_tags.id')
        ;
      }
      $query->orHaving('customerTag', 'like', "%{$fulltextSearch}%");

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
          ->addSelect("customer_tags.name as customerTag")
          ->leftJoin('cross_customer_tags', 'cross_customer_tags.id_customer', '=', 'customers.id')
          ->leftJoin('customer_tags', 'cross_customer_tags.id_tag', '=', 'customer_tags.id')
        ;
      }
      $query->having('customerTag', 'like', "%{$columnSearch['tags']}%");
    }
    return $query;
  }

}
