<?php

namespace HubletoApp\Community\Customers\Models\Eloquent;

use HubletoApp\Community\Documents\Models\Eloquent\Document;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompanyDocument extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'company_documents';

  public function DOCUMENT(): BelongsTo {
    return $this->belongsTo(Document::class, 'id_document', 'id');
  }
  public function COMPANY(): BelongsTo {
    return $this->belongsTo(Company::class, 'id_company', 'id');
  }
}
