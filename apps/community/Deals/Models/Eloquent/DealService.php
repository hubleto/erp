<?php

namespace HubletoApp\Community\Deals\Models\Eloquent;

use HubletoApp\Community\Services\Models\Eloquent\Service;
use HubletoApp\Community\Deals\Models\Eloquent\Deal;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealService extends \HubletoMain\Core\ModelEloquent
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
