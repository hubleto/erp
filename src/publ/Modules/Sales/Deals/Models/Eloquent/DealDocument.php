<?php

namespace CeremonyCrmApp\Modules\Sales\Deals\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Documents\Models\Eloquent\Document;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DealDocument extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'deal_documents';

  public function DOCUMENT(): BelongsTo {
    return $this->belongsTo(Document::class, 'id_document', 'id');
  }
  public function DEAL(): BelongsTo {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }
}
