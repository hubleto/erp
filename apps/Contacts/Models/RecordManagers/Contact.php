<?php

namespace Hubleto\App\Community\Contacts\Models\RecordManagers;

use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contact extends \Hubleto\Erp\RecordManager
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

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    if ($hubleto->router()->urlParamAsInteger("idCustomer") > 0) {
      $query = $query->where($this->table . '.id_customer', $hubleto->router()->urlParamAsInteger("idCustomer"));
    }

    // Virtual number
    $query->selectSub(function($sub) {
      $sub->from('contact_values')
        ->select('value')
        ->whereColumn('contact_values.id_contact', 'contacts.id')
        ->where('type', 'number')
        ->limit(1);
    }, 'virt_number');

    // Virtual email
    $query->selectSub(function($sub) {
      $sub->from('contact_values')
        ->select('value')
        ->whereColumn('contact_values.id_contact', 'contacts.id')
        ->where('type', 'email')
        ->limit(1);
    }, 'virt_email');

    // Virtual tag list (aggregated)
    $query->selectSub(function($sub) {
      $sub->from('contact_contact_tags')
        ->join('contact_tags', 'contact_tags.id', '=', 'contact_contact_tags.id_tag')
        ->whereColumn('contact_contact_tags.id_contact', 'contacts.id')
        ->selectRaw("GROUP_CONCAT(DISTINCT contact_tags.name ORDER BY contact_tags.name SEPARATOR ', ')");
    }, 'contactTag');

    // Virtual tag count
    $query->selectSub(function($sub) {
      $sub->from('contact_contact_tags')
        ->join('contact_tags', 'contact_tags.id', '=', 'contact_contact_tags.id_tag')
        ->whereColumn('contact_contact_tags.id_contact', 'contacts.id')
        ->selectRaw("COUNT(DISTINCT contact_tags.id)");
    }, 'tags_count');

    return $query;
  }

   public function addOrderByToQuery(mixed $query, array $orderBy): mixed
   {
     if (($orderBy['field'] ?? null) === 'virt_tags') {
       return $query->orderBy('tags_count', $orderBy['direction']);
     }
     return parent::addOrderByToQuery($query, $orderBy);
   }

   public function addFulltextSearchToQuery(mixed $query, string $fulltextSearch): mixed
   {
     if (!empty($fulltextSearch)) {
       $query = parent::addFulltextSearchToQuery($query, $fulltextSearch);
       $like = "%{$fulltextSearch}%";
       $query->orHaving('contactTag', 'like', "%{$like}%");
       $query->orHaving('virt_email', 'like', "%{$like}%");
       $query->orHaving('virt_number', 'like', "%{$like}%");
     }
     return $query;
   }

   public function addColumnSearchToQuery(mixed $query, array $columnSearch): mixed
   {
     $query = parent::addColumnSearchToQuery($query, $columnSearch);

     if (!empty($columnSearch['virt_tags'] ?? '')) {
       $query->having('contactTag', 'like', "%{$columnSearch['virt_tags']}%");
     }
     if (!empty($columnSearch['virt_email'] ?? '')) {
       $query->having('virt_email', 'like', "%{$columnSearch['virt_email']}%");
     }
     if (!empty($columnSearch['virt_number'] ?? '')) {
       $query->having('virt_number', 'like', "%{$columnSearch['virt_number']}%");
     }
     return $query;
   }

  public function prepareLookupQuery(string $search): mixed
  {
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idCustomer = $hubleto->router()->urlParamAsInteger('idCustomer');

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
