<?php

namespace HubletoApp\Documents\Models\Eloquent;

use HubletoApp\Customers\Models\Eloquent\CompanyDocument;
use HubletoApp\Leads\Models\Eloquent\LeadDocument;
use HubletoApp\Deals\Models\Eloquent\DealDocument;
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
