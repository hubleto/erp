<?php

namespace Hubleto\App\Community\Mail\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;

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
    $idAccount = $main->router()->urlParamAsInteger('idAccount');
    $idMailbox = $main->router()->urlParamAsInteger('idMailbox');
    $showOnlyScheduledToSend = $main->router()->urlParamAsBool('showOnlyScheduledToSend');
    $showOnlyDrafts = $main->router()->urlParamAsBool('showOnlyDrafts');
    $showOnlyTemplates = $main->router()->urlParamAsBool('showOnlyTemplates');

    $query = parent::prepareReadQuery($query, $level);

    if ($idAccount > 0) $query = $query->where('mails.id_account', $idAccount);
    if ($idMailbox > 0) $query = $query->where('mails.id_mailbox', $idMailbox);
    if ($showOnlyScheduledToSend) $query = $query->whereNotNull('mails.datetime_scheduled_to_send')->whereNull('mails.datetime_sent');
    if ($showOnlyDrafts) $query = $query->where('mails.is_draft', true);
    if ($showOnlyTemplates) $query = $query->where('mails.is_template', true);

    return $query;
  }

}
