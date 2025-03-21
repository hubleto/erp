<?php

namespace HubletoApp\Community\Customers\Models\Eloquent;

use HubletoApp\Community\Documents\Models\Eloquent\Document;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerDocument extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'customer_documents';

  /** @return BelongsTo<Document, covariant CustomerDocument> */
  public function DOCUMENT(): BelongsTo {
    return $this->belongsTo(Document::class, 'id_document', 'id');
  }

  /** @return BelongsTo<Customer, covariant CustomerDocument> */
  public function CUSTOMER(): BelongsTo {
    return $this->belongsTo(Customer::class, 'id_lookup', 'id');
  }
}
