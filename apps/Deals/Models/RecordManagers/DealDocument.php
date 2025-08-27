<?php

namespace HubletoApp\Community\Deals\Models\RecordManagers;

use HubletoApp\Community\Documents\Models\RecordManagers\Document;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DealDocument extends \HubletoMain\RecordManager
{
  public $table = 'deal_documents';

  /** @return BelongsTo<Document, covariant DealDocument> */
  public function DOCUMENT(): BelongsTo
  {
    return $this->belongsTo(Document::class, 'id_document', 'id');
  }

  /** @return BelongsTo<Deal, covariant DealDocument> */
  public function DEAL(): BelongsTo
  {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }

}
