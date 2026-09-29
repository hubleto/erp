<?php

namespace Hubleto\App\Community\HrPerformance\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends \Hubleto\Erp\RecordManager
{
  public $table = 'hr_reviews';

  public function EMPLOYEE(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_user', 'id');
  }

  public function REVIEWER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_reviewer', 'id');
  }
}