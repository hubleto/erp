<?php

namespace Hubleto\App\Community\Settings\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceProfile extends \Hubleto\Erp\RecordManager
{
  public $table = 'invoice_profiles';

  /** @return BelongsTo<User, covariant User> */
  public function SUPPLIER(): BelongsTo
  {
    return $this->belongsTo(Company::class, 'id_supplier', 'id');
  }

}
