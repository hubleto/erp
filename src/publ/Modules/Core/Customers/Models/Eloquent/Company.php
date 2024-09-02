<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Billing\Models\Eloquent\BillingAccount;
use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\Country;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'companies';

  public function PERSONS(): HasMany {
    return $this->hasMany(Person::class, 'id_company');
  }
   public function id_country(): HasOne {
    return $this->hasOne(Country::class, 'id', 'id_country' );
  }
  public function COUNTRY(): HasOne {
    return $this->id_country();
  }
  public function FIRST_CONTACT(): HasOne {
    return $this->hasOne(Person::class, 'id_company')->where("is_primary", true);
  }
  public function BILLING_ACCOUNT(): HasMany {
    return $this->hasMany(BillingAccount::class, 'id_company');
  }
  public function ACTIVITIES(): HasMany {
    return $this->hasMany(Activity::class, 'id_company');
  }
  public function TAGS(): HasMany
  {
    return $this->hasMany(CompanyTag::class, 'id_company', 'id');
  }
}
