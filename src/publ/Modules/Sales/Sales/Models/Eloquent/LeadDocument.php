<?php

namespace CeremonyCrmApp\Modules\Sales\Sales\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Documents\Models\Eloquent\Document;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeadDocument extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'lead_documents';

  public function DOCUMENT(): BelongsTo {
    return $this->belongsTo(Document::class, 'id_document', 'id');
  }
  public function LEAD(): BelongsTo {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }
}
