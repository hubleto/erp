<?php

namespace HubletoApp\Community\Messages\Models\RecordManagers;

use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Settings\Models\RecordManagers\User;

class Message extends \HubletoMain\Core\RecordManager
{
  public $table = 'messages';

  /** @return BelongsTo<User, covariant Customer> */
  public function OWNER(): BelongsTo {
    return $this->belongsTo(User::class, 'id_owner', 'id');
  }

}
