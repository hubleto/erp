<?php

namespace HubletoApp\Community\Leads\Models\Eloquent;

use HubletoApp\Community\Settings\Models\Eloquent\Tag;
use HubletoApp\Community\Leads\Models\Eloquent\Lead;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeadTag extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'lead_tags';

  public function LEAD(): BelongsTo {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }
  public function TAG(): BelongsTo {
    return $this->belongsTo(Tag::class, 'id_tag', 'id');
  }
}
