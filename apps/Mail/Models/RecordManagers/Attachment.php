<?php

namespace Hubleto\App\Community\Mail\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends \Hubleto\Erp\RecordManager
{
  public $table = 'mails_attachments';

  /** @return BelongsTo<User, covariant Customer> */
  public function MAIL(): BelongsTo {
    return $this->belongsTo(Mail::class, 'id_mail', 'id');
  }

}
