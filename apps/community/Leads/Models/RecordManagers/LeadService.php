<?php

namespace HubletoApp\Community\Leads\Models\RecordManagers;

use HubletoApp\Community\Services\Models\RecordManagers\Service;
use HubletoApp\Community\Leads\Models\RecordManagers\Lead;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadService extends \HubletoMain\Core\RecordManager
{
  public $table = 'lead_services';

  /** @return BelongsTo<Lead, covariant LeadService> */
  public function LEAD(): BelongsTo {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }

  /** @return BelongsTo<Service, covariant LeadService> */
  public function SERVICE(): BelongsTo {
    return $this->belongsTo(Service::class, 'id_service', 'id');
  }

}
