<?php

namespace Hubleto\App\Community\Invoices\Models\RecordManagers;

use Hubleto\App\Community\Settings\Models\RecordManagers\Company;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Hubleto\App\Community\Documents\Models\RecordManagers\Template;

class Profile extends \Hubleto\Erp\RecordManager
{
  public $table = 'invoice_profiles';

  /** @return BelongsTo<User, covariant User> */
  public function COMPANY(): BelongsTo
  {
    return $this->belongsTo(Company::class, 'id_company', 'id');
  }

  /** @return hasOne<Currency, covariant Lead> */
  public function TEMPLATE(): HasOne
  {
    return $this->hasOne(Template::class, 'id', 'id_template');
  }

}
