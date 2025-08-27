<?php

namespace Hubleto\App\Community\EventRegistrations\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;

class Contact extends \Hubleto\Erp\RecordManager
{
  public $table = 'my_app_contacts';

  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

}
