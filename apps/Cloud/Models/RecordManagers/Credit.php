<?php

namespace HubletoApp\Community\Cloud\Models\RecordManagers;

class Credit extends \Hubleto\Framework\RecordManager
{
  public $table = 'cloud_credit';

  public function recordDelete(int|string $id): int
  {
    return 0;
  }
}
