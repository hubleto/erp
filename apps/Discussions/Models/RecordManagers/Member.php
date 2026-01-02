<?php

namespace Hubleto\App\Community\Discussions\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Member extends \Hubleto\Erp\RecordManager
{
  public $table = 'discussions_members';

  public function DISCUSSION(): BelongsTo
  {
    return $this->belongsTo(Discussion::class, 'id_discussion', 'id');
  }

  public function MEMBER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_member', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    if ($hubleto->router()->urlParamAsInteger("idDiscussion") > 0) {
      $query = $query->where($this->table . '.id_discussion', $hubleto->router()->urlParamAsInteger("idDiscussion"));
    }

    // Uncomment and modify these lines if you want to apply default filters to your model.
    // $filters = $hubleto->router()->urlParamAsArray("filters");
    // if (isset($filters["fArchive"]) && $filters["fArchive"] == 1) $query = $query->where("customers.is_active", false);
    // else $query = $query->where("customers.is_active", true);

    return $query;
  }

}
