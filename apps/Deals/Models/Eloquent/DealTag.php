<?php

namespace HubletoApp\Deals\Models\Eloquent;

use HubletoApp\Settings\Models\Eloquent\Tag;
use HubletoApp\Deals\Models\Eloquent\Deal;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DealTag extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'deal_tags';

  public function DEAL(): BelongsTo {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }
  public function TAG(): BelongsTo {
    return $this->belongsTo(Tag::class, 'id_tag', 'id');
  }

}
