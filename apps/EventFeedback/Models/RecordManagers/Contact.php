<?php

namespace Hubleto\App\Community\EventFeedback\Models\RecordManagers;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends \Hubleto\Erp\RecordManager
{
  public $table = 'my_app_contacts';

  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

}
