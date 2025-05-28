<?php

namespace HubletoApp\Community\Cloud\Models\RecordManagers;

class Payment extends \HubletoMain\Core\RecordManager
{
  public $table = 'cloud_payments';

  public function recordDelete(int|string $id): int
  {
    return 0;
  }
}