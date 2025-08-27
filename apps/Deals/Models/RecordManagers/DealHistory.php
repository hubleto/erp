<?php

namespace Hubleto\App\Community\Deals\Models\RecordManagers;

use Hubleto\App\Community\Deals\Models\RecordManagers\Deal;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealHistory extends \Hubleto\Erp\RecordManager
{
  public $table = 'deal_histories';

  /** @return BelongsTo<Deal, covariant DealHistory> */
  public function DEAL(): BelongsTo
  {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }

}
