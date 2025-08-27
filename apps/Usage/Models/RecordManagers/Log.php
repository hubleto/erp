<?php

namespace HubletoApp\Community\Usage\Models\RecordManagers;

class Log extends \Hubleto\Erp\RecordManager
{
  public $table = 'usage_log';

  public function recordDelete(int|string $id): int
  {
    return 0;
  }
}
