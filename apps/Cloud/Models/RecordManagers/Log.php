<?php

namespace HubletoApp\Community\Cloud\Models\RecordManagers;

class Log extends \HubletoMain\RecordManager
{
  public $table = 'cloud_log';

  public function recordDelete(int|string $id): int
  {
    return 0;
  }
}
