<?php

namespace Hubleto\App\Community\Mail\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Hubleto\App\Community\Auth\Models\RecordManagers\User;

class Account extends \Hubleto\Erp\RecordManager
{
  public $table = 'mails_accounts';

  /** @return BelongsTo<User, covariant Deal> */
  public function OWNER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_owner', 'id');
  }

  /** @return BelongsTo<User, covariant Lead> */
  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

  /** @return HasMany<DealTask, covariant Deal> */
  public function MAILBOXES(): HasMany
  {
    return $this->hasMany(Mailbox::class, 'id_account', 'id');
  }
}
