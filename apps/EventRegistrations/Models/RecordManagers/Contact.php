<?php

namespace Hubleto\App\Community\EventRegistrations\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends \Hubleto\Erp\RecordManager
{
  public $table = 'my_app_contacts';

  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

}
