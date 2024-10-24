<?php

namespace CeremonyCrmApp\Modules\Core\Billing\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Services\Models\Eloquent\Service;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BillingAccountService extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'billing_accounts_services';

  public function BILLING_ACCOUNT(): BelongsTo {
    return $this->belongsTo(BillingAccount::class, 'id_billing_account','id');
  }
  public function SERVICE(): BelongsTo {
    return $this->belongsTo(Service::class, 'id_service', 'id');
  }
}
