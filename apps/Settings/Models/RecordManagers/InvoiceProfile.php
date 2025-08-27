<?php

namespace HubletoApp\Community\Settings\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceProfile extends \HubletoMain\RecordManager
{
  public $table = 'invoice_profiles';

  /** @return BelongsTo<User, covariant User> */
  public function SUPPLIER(): BelongsTo
  {
    return $this->belongsTo(Company::class, 'id_supplier', 'id');
  }

}
