<?php

namespace Hubleto\App\Community\Discussions\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;

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

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    if ($main->getRouter()->urlParamAsInteger("idDiscussion") > 0) {
      $query = $query->where($this->table . '.id_discussion', $main->getRouter()->urlParamAsInteger("idDiscussion"));
    }

    // Uncomment and modify these lines if you want to apply default filters to your model.
    // $defaultFilters = $main->getRouter()->urlParamAsArray("defaultFilters");
    // if (isset($defaultFilters["fArchive"]) && $defaultFilters["fArchive"] == 1) $query = $query->where("customers.is_active", false);
    // else $query = $query->where("customers.is_active", true);

    return $query;
  }

}
