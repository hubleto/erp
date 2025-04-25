<?php

namespace HubletoApp\Community\Premium\Models\RecordManagers;

class Credit extends \HubletoMain\Core\RecordManager
{
  public $table = 'premium_credit';

  public function recordDelete(int|string $id): int
  {
    return 0;
  }
}