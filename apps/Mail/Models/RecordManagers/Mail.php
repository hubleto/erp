<?php

namespace Hubleto\App\Community\Mail\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;

class Mail extends \Hubleto\Erp\RecordManager
{
  public $table = 'mails';

  /** @return BelongsTo<User, covariant Customer> */
  public function FOLDER(): BelongsTo {
    return $this->belongsTo(Mailbox::class, 'id_mailbox', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $main = \Hubleto\Erp\Loader::getGlobalApp();
    $idMailbox = $main->getRouter()->urlParamAsInteger('idMailbox');
    $showOnlyDrafts = $main->getRouter()->urlParamAsBool('showOnlyDrafts');
    $showOnlyTemplates = $main->getRouter()->urlParamAsBool('showOnlyTemplates');

    $query = parent::prepareReadQuery($query, $level);

    if ($idMailbox > 0) $query = $query->where('id_mailbox', $idMailbox);
    if ($showOnlyDrafts) $query = $query->where('is_draft', true);
    if ($showOnlyTemplates) $query = $query->where('is_template', true);

    return $query;
  }

}
