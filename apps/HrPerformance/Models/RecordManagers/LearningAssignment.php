<?php

namespace Hubleto\App\Community\HrPerformance\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Hubleto\App\Community\HrPerformance\Models\RecordManagers\Course;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningAssignment extends \Hubleto\Erp\RecordManager
{
  public $table = 'hr_learning_assignments';

  public function USER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_user', 'id');
  }

  public function COURSE(): BelongsTo
  {
    return $this->belongsTo(Course::class, 'id_course', 'id');
  }
}