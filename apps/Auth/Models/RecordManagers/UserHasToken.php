<?php

namespace Hubleto\App\Community\Auth\Models\RecordManagers;

class UserHasToken extends \Hubleto\Erp\RecordManager {
  public static $snakeAttributes = false;
  public $table = 'user_has_tokens';

}
