<?php

namespace HubletoApp\Community\EventRegistrations\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Settings\Models\RecordManagers\User;

class Contact extends \HubletoMain\RecordManager
{
  public $table = 'my_app_contacts';

  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

}
