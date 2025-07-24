<?php

namespace HubletoApp\Community\Billing\Models\RecordManagers;

use HubletoApp\Community\Services\Models\RecordManagers\Service;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BillingAccountService extends \Hubleto\Framework\RecordManager
{
  public $table = 'billing_accounts_services';

  /** @return BelongsTo<BillingAccount, covariant BillingAccountService> */
  public function BILLING_ACCOUNT(): BelongsTo
  {
    return $this->belongsTo(BillingAccount::class, 'id_billing_account', 'id');
  }

  /** @return BelongsTo<Service, covariant BillingAccountService> */
  public function SERVICE(): BelongsTo
  {
    return $this->belongsTo(Service::class, 'id_service', 'id');
  }
}
