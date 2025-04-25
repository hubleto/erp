<?php

namespace HubletoApp\Community\Premium\Models\RecordManagers;

class Log extends \HubletoMain\Core\RecordManager
{
  public $table = 'premium_log';

  public function recordDelete(int|string $id): int
  {
    return 0;
  }
}