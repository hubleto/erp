<?php

namespace Hubleto\App\Community\Customers\Models\RecordManagers;


use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Hubleto\App\Community\Contacts\Models\RecordManagers\Contact;
use Hubleto\App\Community\Deals\Models\RecordManagers\Deal;
use Hubleto\App\Community\Leads\Models\RecordManagers\Lead;
use Hubleto\App\Community\Settings\Models\RecordManagers\Country;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends \Hubleto\Erp\RecordManager
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
    return $this->hasMany(CustomerDocument::class, 'id_customer', 'id');
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

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    $filters = $hubleto->router()->urlParamAsArray("filters");
    if (isset($filters["fCustomerActive"])) {
      if ($filters["fCustomerActive"] == 1) {
        $query = $query->where("customers.is_active", false);
      } else {
        $query = $query->where("customers.is_active", true);
      }
    }

    // Virtual tag count
    $query->selectSub(function($sub) {
      $sub->from('cross_customer_tags')
        ->join('customer_tags', 'customer_tags.id', '=', 'cross_customer_tags.id_tag')
        ->whereColumn('cross_customer_tags.id_customer', 'customers.id')
        ->selectRaw("COUNT(DISTINCT customer_tags.id)");
    }, 'tags_count');

    return $query;
  }

  public function addOrderByToQuery(mixed $query, array $orderBy): mixed
  {
    if (($orderBy['field'] ?? null) === 'virt_tags') {
      return $query->orderBy('tags_count', $orderBy['direction']);
    } else {
      return parent::addOrderByToQuery($query, $orderBy);
    }
  }

  public function addFulltextSearchToQuery(mixed $query, string $fulltextSearch): mixed
  {
    if (!empty($fulltextSearch)) {
      $query = parent::addFulltextSearchToQuery($query, $fulltextSearch);

      $like = "%{$fulltextSearch}%";
      $query->orHaving('virt_tags', 'like', "%{$like}%");
    }
    return $query;
  }

}
