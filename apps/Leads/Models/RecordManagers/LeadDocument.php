<?php

namespace HubletoApp\Community\Leads\Models\RecordManagers;

use HubletoApp\Community\Documents\Models\RecordManagers\Document;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeadDocument extends \Hubleto\Framework\RecordManager
{
  public $table = 'lead_documents';

  /** @return BelongsTo<Document, covariant LeadDocument> */
  public function DOCUMENT(): BelongsTo
  {
    return $this->belongsTo(Document::class, 'id_document', 'id');
  }

  /** @return BelongsTo<Lead, covariant LeadDocument> */
  public function LEAD(): BelongsTo
  {
    return $this->belongsTo(Lead::class, 'id_lookup', 'id');
  }

}
