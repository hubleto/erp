<?php

namespace HubletoApp\Community\Customers\Models\Eloquent;

use HubletoApp\Community\Billing\Models\Eloquent\BillingAccount;
use HubletoApp\Community\Customers\Models\Eloquent\CompanyDocument;
use HubletoApp\Community\Settings\Models\Eloquent\Country;
use HubletoApp\Community\Settings\Models\Eloquent\User;
use HubletoApp\Community\Deals\Models\Eloquent\Deal;
use HubletoApp\Community\Leads\Models\Eloquent\Lead;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'companies';

  public function PERSONS(): HasMany {
    return $this->hasMany(Person::class, 'id_company');
  }
  public function COUNTRY(): HasOne {
    return $this->hasOne(Country::class, 'id', 'id_country' );
  }
  public function FIRST_CONTACT(): HasOne {
    return $this->hasOne(Person::class, 'id_company')->where('is_main', true);
  }
  /* public function BILLING_ACCOUNTS(): HasMany {
    return $this->hasMany(BillingAccount::class, 'id_company');
  } */
  public function ACTIVITIES(): HasMany {
    return $this->hasMany(CompanyActivity::class, 'id_company', 'id' );
  }
  public function DOCUMENTS(): HasMany {
    return $this->hasMany(CompanyDocument::class, 'id_company', 'id' );
  }
  public function TAGS(): HasMany {
    return $this->hasMany(CompanyTag::class, 'id_company', 'id');
  }
  public function LEADS(): HasMany {
    return $this->hasMany(Lead::class, 'id_company', 'id');
  }
  public function DEALS(): HasMany {
    return $this->hasMany(Deal::class, 'id_company', 'id');
  }
  public function USER(): BelongsTo {
    return $this->belongsTo(User::class, 'id_user', 'id');
  }
}
