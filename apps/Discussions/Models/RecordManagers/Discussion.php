<?php

namespace Hubleto\App\Community\Discussions\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discussion extends \Hubleto\Erp\RecordManager
{
  public $table = 'discussions';

  public function MAIN_MOD(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_main_mod', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    return parent::prepareReadQuery($query, $level, $includeRelations);
  }

}
