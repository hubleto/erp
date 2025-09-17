<?php

namespace Hubleto\App\Community\Auth\Models;

use Hubleto\Erp\Model;

class UserHasToken extends Model {

  public string $table = "user_has_tokens";
  public string $recordManagerClass = RecordManagers\UserHasToken::class;
  public bool $isJunctionTable = FALSE;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_user' => new \Hubleto\Framework\Db\Column\Lookup($this, 'User', \Hubleto\Framework\Models\User::class),
      'id_token' => new \Hubleto\Framework\Db\Column\Lookup($this, 'Token', Token::class),
    ]);
  }
}
