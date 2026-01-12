<?php

namespace Hubleto\App\Community\Mail\Models\RecordManagers;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

  /** @return BelongsTo<User, covariant Customer> */
  public function MAILS(): HasMany {
    return $this->hasMany(Mail::class, 'id_mail');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idAccount = $hubleto->router()->urlParamAsInteger('idAccount');
    $idMailbox = $hubleto->router()->urlParamAsInteger('idMailbox');
    $showOnlyScheduledToSend = $hubleto->router()->urlParamAsBool('showOnlyScheduledToSend');
    $showOnlySent = $hubleto->router()->urlParamAsBool('showOnlySent');
    $showOnlyDrafts = $hubleto->router()->urlParamAsBool('showOnlyDrafts');
    $showOnlyTemplates = $hubleto->router()->urlParamAsBool('showOnlyTemplates');

    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    if ($idAccount > 0) $query = $query->where('mails.id_account', $idAccount);
    if ($idMailbox > 0) $query = $query->where('mails.id_mailbox', $idMailbox);
    if ($showOnlyScheduledToSend) $query = $query->whereNotNull('mails.datetime_scheduled_to_send')->whereNull('mails.datetime_sent');
    if ($showOnlySent) $query = $query->whereNotNull('mails.datetime_sent');
    if ($showOnlyDrafts) $query = $query->where('mails.is_draft', true);
    if ($showOnlyTemplates) $query = $query->where('mails.is_template', true);

    return $query;
  }

}
