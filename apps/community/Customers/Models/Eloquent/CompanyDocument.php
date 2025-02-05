<?php

namespace HubletoApp\Community\Customers\Models\Eloquent;

use HubletoApp\Community\Documents\Models\Eloquent\Document;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompanyDocument extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'company_documents';

  /** @return BelongsTo<Document, covariant CompanyDocument> */
  public function DOCUMENT(): BelongsTo {
    return $this->belongsTo(Document::class, 'id_document', 'id');
  }

  /** @return BelongsTo<Company, covariant CompanyDocument> */
  public function COMPANY(): BelongsTo {
    return $this->belongsTo(Company::class, 'id_company', 'id');
  }
}
