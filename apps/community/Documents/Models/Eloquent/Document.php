<?php

namespace HubletoApp\Community\Documents\Models\Eloquent;

use HubletoApp\Community\Customers\Models\Eloquent\CustomerDocument;
use HubletoApp\Community\Leads\Models\Eloquent\LeadDocument;
use HubletoApp\Community\Deals\Models\Eloquent\DealDocument;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Document extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'documents';

  /** @return hasOne<CustomerDocument, covariant Document> */
  public function CUSTOMER_DOCUMENT(): HasOne {
    return $this->hasOne(CustomerDocument::class, 'id_document', 'id');
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
