<?php

namespace Hubleto\App\Community\Documents\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentReview extends \Hubleto\Erp\RecordManager
{
  public $table = 'documents_reviews';

  /** @return BelongsTo<Customer, covariant BillingAccount> */
  public function DOCUMENT(): BelongsTo
  {
    return $this->belongsTo(Document::class, 'id_document', 'id');
  }

  /** @return BelongsTo<Customer, covariant BillingAccount> */
  public function REQUESTED_BY(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_requested_by', 'id');
  }

  /** @return BelongsTo<Customer, covariant BillingAccount> */
  public function REVIEWED_BY(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_reviewed_by', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idDocument = $hubleto->router()->urlParamAsInteger("idDocument");

    if ($idDocument > 0) $query = $query->where($this->table . '.id_document', $idDocument);

    return $query;
  }

}
