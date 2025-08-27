<?php

namespace HubletoApp\Community\Projects\Models\RecordManagers;

use HubletoApp\Community\Deals\Models\RecordManagers\Deal;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDeal extends \HubletoMain\RecordManager
{
  public $table = 'projects_deals';

  /** @return BelongsTo<Project, covariant ProjectProduct> */
  public function PROJECT(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'id_project', 'id');
  }

  /** @return BelongsTo<Product, covariant OrderProduct> */
  public function DEAL(): BelongsTo
  {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }
}
