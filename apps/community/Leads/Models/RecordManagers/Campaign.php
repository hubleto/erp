<?php

namespace HubletoApp\Community\Leads\Models\RecordManagers;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Campaign extends \HubletoMain\Core\RecordManager
{
  public $table = 'lead_campaigns';

  /** @return HasMany<CustomerDocument, covariant Customer> */
  public function LEADS(): HasMany {
    return $this->hasMany(Lead::class, 'id_campaign', 'id' );
  }

}
