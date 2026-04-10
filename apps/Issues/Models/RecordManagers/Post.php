<?php

namespace Hubleto\App\Community\Issues\Models\RecordManagers;

use Hubleto\App\Community\Mail\Models\RecordManagers\Mail;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends \Hubleto\Erp\RecordManager
{
  public $table = 'issues_posts';

  /** @return HasOne<WorkflowStep, covariant Deal> */
  public function ISSUE(): BelongsTo
  {
    return $this->belongsTo(Issue::class, 'id_issue', 'id');
  }

  /** @return HasOne<WorkflowStep, covariant Deal> */
  public function MAIL(): HasOne
  {
    return $this->hasOne(Mail::class, 'id', 'id_mail');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    $idIssue = $hubleto->router()->urlParamAsInteger("idIssue");

    if ($idIssue > 0) $query = $query->where($this->table . '.id_issue', $idIssue);

    return $query;
  }

}
