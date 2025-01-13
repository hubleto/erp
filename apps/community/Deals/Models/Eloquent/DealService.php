<?php

namespace HubletoApp\Community\Deals\Models\Eloquent;

use HubletoApp\Community\Services\Models\Eloquent\Service;
use HubletoApp\Community\Deals\Models\Eloquent\Deal;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealService extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'deal_services';

  public function DEAL(): BelongsTo {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }
  public function SERVICE(): BelongsTo {
    return $this->belongsTo(Service::class, 'id_service', 'id');
  }

}
