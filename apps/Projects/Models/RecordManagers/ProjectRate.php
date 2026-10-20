<?php

namespace Hubleto\App\Community\Projects\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectRate extends \Hubleto\Erp\RecordManager
{
  public $table = 'projects_rates';

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function PROJECT(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'id_project', 'id');
  }

  /** @return BelongsTo<User, covariant LeadTag> */
  public function USER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_user', 'id');
  }

}
