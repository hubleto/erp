<?php

namespace HubletoApp\Community\Customers\Models\RecordManagers;

use HubletoApp\Community\Documents\Models\RecordManagers\Document;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerDocument extends \Hubleto\Framework\RecordManager
{
  public $table = 'customer_documents';

  /** @return BelongsTo<Document, covariant CustomerDocument> */
  public function DOCUMENT(): BelongsTo
  {
    return $this->belongsTo(Document::class, 'id_document', 'id');
  }

  /** @return BelongsTo<Customer, covariant CustomerDocument> */
  public function CUSTOMER(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'id_lookup', 'id');
  }
}
