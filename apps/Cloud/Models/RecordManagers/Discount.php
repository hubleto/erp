<?php

namespace HubletoApp\Community\Cloud\Models\RecordManagers;

class Discount extends \Hubleto\Framework\RecordManager
{
  public $table = 'cloud_discounts';

  public function recordDelete(int|string $id): int
  {
    return 0;
  }
}
