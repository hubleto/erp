<?php

namespace HubletoApp\Community\Leads\Models\Eloquent;

use HubletoApp\Community\Customers\Models\Eloquent\Company;
use HubletoApp\Community\Customers\Models\Eloquent\Person;
use HubletoApp\Community\Settings\Models\Eloquent\Currency;
use HubletoApp\Community\Settings\Models\Eloquent\User;
use HubletoApp\Community\Deals\Models\Eloquent\Deal;
use HubletoApp\Community\Leads\Models\Eloquent\LeadHistory;
use HubletoApp\Community\Leads\Models\Eloquent\LeadTag;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'leads';

  public function DEAL(): HasOne {
    return $this->hasOne(Deal::class, 'id_lead', 'id' );
  }
  public function COMPANY(): BelongsTo {
    return $this->belongsTo(Company::class, 'id_company', 'id' );
  }
  public function USER(): BelongsTo {
    return $this->belongsTo(User::class, 'id_user','id' );
  }
  public function PERSON(): HasOne {
    return $this->hasOne(Person::class, 'id', 'id_person');
  }
  public function CURRENCY(): HasOne {
    return $this->hasOne(Currency::class, 'id', 'id_currency');
  }
  public function STATUS(): HasOne {
    return $this->hasOne(LeadStatus::class, 'id', 'id_lead_status');
  }
  public function HISTORY(): HasMany {
    return $this->hasMany(LeadHistory::class, 'id_lead', 'id');
  }
  public function TAGS(): HasMany {
    return $this->hasMany(LeadTag::class, 'id_lead', 'id');
  }
  public function SERVICES(): HasMany {
    return $this->hasMany(LeadService::class, 'id_lead', 'id');
  }
  public function ACTIVITIES(): HasMany {
    return $this->hasMany(LeadActivity::class, 'id_lead', 'id' );
  }
  public function DOCUMENTS(): HasMany {
    return $this->hasMany(LeadDocument::class, 'id_lead', 'id' );
  }
}
