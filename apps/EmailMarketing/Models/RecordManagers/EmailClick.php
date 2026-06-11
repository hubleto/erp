<?php

namespace Hubleto\App\Community\EmailMarketing\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmailClick extends \Hubleto\Erp\RecordManager
{
  public $table = 'email_marketing_email_clicks';

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function EMAIL(): BelongsTo
  {
    return $this->belongsTo(Email::class, 'id_email', 'id');
  }

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function RECIPIENT(): BelongsTo
  {
    return $this->belongsTo(EmailRecipient::class, 'id_recipient', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    if ($hubleto->router()->isUrlParam("idEmail")) {
      $query = $query->where($this->table . '.id_email', $hubleto->router()->urlParamAsInteger("idEmail"));
    }

    return $query;
  }
}
