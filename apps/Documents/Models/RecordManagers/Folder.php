<?php

namespace Hubleto\App\Community\Documents\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Folder extends \Hubleto\Erp\RecordManager
{
  public $table = 'folders';

  /** @return BelongsTo<Customer, covariant BillingAccount> */
  public function PARENT_FOLDER(): BelongsTo
  {
    return $this->belongsTo(Folder::class, 'id_parent_folder', 'id');
  }

  public function recordCreate(array $record, $useProvidedRecordId = false): array
  {
    if (!isset($record['uid'])) {
      $record['uid'] = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
    }
    return parent::recordCreate($record);
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    //disables the _ROOT_ folder record from opening
    $query = parent::prepareReadQuery($query, $level, $includeRelations);
    $query->where($this->table.".id", "!=", 1);
    return $query;
  }


  public function prepareLookupQuery(string $search): mixed
  {
    //restric the folder to be moved to itself
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $record = $hubleto->router()->urlParamAsArray("formRecord");
    $query = parent::prepareLookupQuery($search);
    if ($hubleto->router()->urlParamAsBool("noSelfParent") && isset($record["id"])) {
      $query->where($this->table.".id", "!=", $record["id"]);
    }

    return $query;
  }
}
