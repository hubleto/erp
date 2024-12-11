<?php

namespace CeremonyCrmMod\Core\Customers\Models\Eloquent;

use CeremonyCrmMod\Core\Documents\Models\Eloquent\Document;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompanyDocument extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'company_documents';

  public function DOCUMENT(): BelongsTo {
    return $this->belongsTo(Document::class, 'id_document', 'id');
  }
  public function COMPANY(): BelongsTo {
    return $this->belongsTo(Company::class, 'id_company', 'id');
  }
}
