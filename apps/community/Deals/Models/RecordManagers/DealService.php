<?php

namespace HubletoApp\Community\Deals\Models\RecordManagers;

use HubletoApp\Community\Services\Models\RecordManagers\Service;
use HubletoApp\Community\Deals\Models\RecordManagers\Deal;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealService extends \HubletoMain\Core\RecordManager
{
  public $table = 'deal_services';

  /** @return BelongsTo<Deal, covariant DealService> */
  public function DEAL(): BelongsTo {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }

  /** @return BelongsTo<Service, covariant DealService> */
  public function SERVICE(): BelongsTo {
    return $this->belongsTo(Service::class, 'id_service', 'id');
  }

}
