<?php

namespace HubletoApp\Community\Discussions\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Settings\Models\RecordManagers\User;
use HubletoApp\Community\Projects\Models\RecordManagers\Project;

class Discussion extends \HubletoMain\RecordManager
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
