<?php

namespace Hubleto\App\Community\Mail\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mail extends \Hubleto\Erp\RecordManager
{
  public $table = 'mails';

  /** @return BelongsTo<User, covariant Customer> */
  public function ACCOUNT(): BelongsTo {
    return $this->belongsTo(Account::class, 'id_account', 'id');
  }

  /** @return BelongsTo<User, covariant Customer> */
  public function MAILBOX(): BelongsTo {
    return $this->belongsTo(Mailbox::class, 'id_mailbox', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $main = \Hubleto\Erp\Loader::getGlobalApp();
    $idMailbox = $main->router()->urlParamAsInteger('idMailbox');
    $showOnlyDrafts = $main->router()->urlParamAsBool('showOnlyDrafts');
    $showOnlyTemplates = $main->router()->urlParamAsBool('showOnlyTemplates');

    $query = parent::prepareReadQuery($query, $level);

    if ($idMailbox > 0) $query = $query->where('id_mailbox', $idMailbox);
    if ($showOnlyDrafts) $query = $query->where('is_draft', true);
    if ($showOnlyTemplates) $query = $query->where('is_template', true);

    return $query;
  }

}
