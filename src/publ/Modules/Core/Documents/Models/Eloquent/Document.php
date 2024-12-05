<?php

namespace CeremonyCrmApp\Modules\Core\Documents\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent\CompanyDocument;
use CeremonyCrmApp\Modules\Sales\Leads\Models\Eloquent\LeadDocument;
use CeremonyCrmApp\Modules\Sales\Deals\Models\Eloquent\DealDocument;
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
