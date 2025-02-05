<?php

namespace HubletoApp\Community\Customers\Models\Eloquent;

use HubletoApp\Community\Customers\Models\Eloquent\Company;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompanyActivity extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'company_activities';

  /** @return BelongsTo<Company, covariant CompanyActivity> */
  public function COMPANY(): BelongsTo {
    return $this->belongsTo(Company::class, 'id_company', 'id');
  }
}
