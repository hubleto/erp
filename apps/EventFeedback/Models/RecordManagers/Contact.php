<?php

namespace HubletoApp\Community\EventFeedback\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Settings\Models\RecordManagers\User;

class Contact extends \Hubleto\Framework\RecordManager
{
  public $table = 'my_app_contacts';

  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

}
