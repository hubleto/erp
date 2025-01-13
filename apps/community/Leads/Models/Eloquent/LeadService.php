<?php

namespace HubletoApp\Community\Leads\Models\Eloquent;

use HubletoApp\Community\Services\Models\Eloquent\Service;
use HubletoApp\Community\Leads\Models\Eloquent\Lead;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadService extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'lead_services';

  public function LEAD(): BelongsTo {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }
  public function SERVICE(): BelongsTo {
    return $this->belongsTo(Service::class, 'id_service', 'id');
  }

}
