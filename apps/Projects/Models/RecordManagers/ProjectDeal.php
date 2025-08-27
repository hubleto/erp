<?php

namespace Hubleto\App\Community\Projects\Models\RecordManagers;

use Hubleto\App\Community\Deals\Models\RecordManagers\Deal;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDeal extends \Hubleto\Erp\RecordManager
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
