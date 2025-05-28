<?php

namespace HubletoApp\Community\Cloud\Models\RecordManagers;

class Log extends \HubletoMain\Core\RecordManager
{
  public $table = 'premium_log';

  public function recordDelete(int|string $id): int
  {
    return 0;
  }
}