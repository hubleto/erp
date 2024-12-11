<?php

namespace CeremonyCrmMod\Core\Documents\Models\Eloquent;

use CeremonyCrmMod\Core\Customers\Models\Eloquent\CompanyDocument;
use CeremonyCrmMod\Sales\Leads\Models\Eloquent\LeadDocument;
use CeremonyCrmMod\Sales\Deals\Models\Eloquent\DealDocument;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Document extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'documents';

  public function COMPANY_DOCUMENT(): HasOne {
    return $this->hasOne(CompanyDocument::class, 'id_document', 'id');
  }
  public function LEAD_DOCUMENT(): HasOne {
    return $this->hasOne(LeadDocument::class, 'id_document', 'id');
  }
  public function DEAL_DOCUMENT(): HasOne {
    return $this->hasOne(DealDocument::class, 'id_document', 'id');
  }
}
