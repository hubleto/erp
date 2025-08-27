<?php

namespace HubletoApp\Community\Usage\Models\RecordManagers;

class Log extends \HubletoMain\RecordManager
{
  public $table = 'usage_log';

  public function recordDelete(int|string $id): int
  {
    return 0;
  }
}
