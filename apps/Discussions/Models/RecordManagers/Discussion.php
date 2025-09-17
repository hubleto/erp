<?php

namespace Hubleto\App\Community\Discussions\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;
use Hubleto\App\Community\Projects\Models\RecordManagers\Project;

class Discussion extends \Hubleto\Erp\RecordManager
{
  public $table = 'discussions';

  public function MAIN_MOD(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_main_mod', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    return parent::prepareReadQuery($query, $level);
  }

}
