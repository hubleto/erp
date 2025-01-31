<?php

namespace HubletoApp\Community\Documents\Models\Eloquent;

use HubletoApp\Community\Customers\Models\Eloquent\CompanyDocument;
use HubletoApp\Community\Leads\Models\Eloquent\LeadDocument;
use HubletoApp\Community\Deals\Models\Eloquent\DealDocument;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Document extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'documents';

  /** @return hasOne<CompanyDocument, covariant Document> */
  public function COMPANY_DOCUMENT(): HasOne {
    return $this->hasOne(CompanyDocument::class, 'id_document', 'id');
  }

  /** @return hasOne<LeadDocument, covariant Document> */
  public function LEAD_DOCUMENT(): HasOne {
    return $this->hasOne(LeadDocument::class, 'id_document', 'id');
  }

  /** @return hasOne<DealDocument, covariant Document> */
  public function DEAL_DOCUMENT(): HasOne {
    return $this->hasOne(DealDocument::class, 'id_document', 'id');
  }
}
