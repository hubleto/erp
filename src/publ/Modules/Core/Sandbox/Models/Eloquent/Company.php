<?php

namespace CeremonyCrmApp\Modules\Core\Sandbox\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\HasOne;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'sbx_companies';

  public function MAIN_PERSON(): HasOne
  {
    return $this->hasOne(Person::class, 'id_company', 'id')->where('is_main', true);
  }

  public function OTHER_PERSONS(): HasMany
  {
    return $this->hasMany(Person::class, 'id_company', 'id')->where('is_main', false);
  }

  public function CATEGORIES(): HasMany
  {
    return $this->hasMany(CompanyCategory::class, 'id_company', 'id');
  }

}
