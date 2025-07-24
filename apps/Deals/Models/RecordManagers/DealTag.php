<?php

namespace HubletoApp\Community\Deals\Models\RecordManagers;

use HubletoApp\Community\Deals\Models\RecordManagers\Tag;
use HubletoApp\Community\Deals\Models\RecordManagers\Deal;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DealTag extends \Hubleto\Framework\RecordManager
{
  public $table = 'cross_deal_tags';

  /** @return BelongsTo<Deal, covariant DealTag> */
  public function DEAL(): BelongsTo
  {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }

  /** @return BelongsTo<Tag, covariant DealTag> */
  public function TAG(): BelongsTo
  {
    return $this->belongsTo(Tag::class, 'id_tag', 'id');
  }

}
