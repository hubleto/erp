<?php

namespace HubletoApp\Community\Cloud\Models\RecordManagers;

class Credit extends \HubletoMain\Core\RecordManager
{
  public $table = 'premium_credit';

  public function recordDelete(int|string $id): int
  {
    return 0;
  }
}