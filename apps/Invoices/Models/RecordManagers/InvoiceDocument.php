<?php

namespace HubletoApp\Community\Invoices\Models\RecordManagers;

use HubletoApp\Community\Documents\Models\RecordManagers\Document;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InvoiceDocument extends \Hubleto\Erp\RecordManager
{
  public $table = 'invoice_documents';

  /** @return BelongsTo<Document, covariant InvoiceDocument> */
  public function DOCUMENT(): BelongsTo
  {
    return $this->belongsTo(Document::class, 'id_document', 'id');
  }

  /** @return BelongsTo<Invoice, covariant InvoiceDocument> */
  public function INVOICE(): BelongsTo
  {
    return $this->belongsTo(Invoice::class, 'id_invoice', 'id');
  }

}
