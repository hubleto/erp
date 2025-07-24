<?php

namespace HubletoApp\Community\Deals\Models\RecordManagers;

use HubletoApp\Community\Deals\Models\RecordManagers\Deal;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealHistory extends \Hubleto\Framework\RecordManager
{
  public $table = 'deal_histories';

  /** @return BelongsTo<Deal, covariant DealHistory> */
  public function DEAL(): BelongsTo
  {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }

}
