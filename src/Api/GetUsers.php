<?php declare(strict_types=1);

namespace Hubleto\Erp\Api;

use \Hubleto\App\Community\Auth\Models\User;

class GetUsers extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $mUser = $this->getModel(User::class);
    $users = $mUser->record
      ->select(['id', 'login', 'email', 'first_name', 'last_name', 'nick', 'photo', 'position'])
      ->with('TEAMS')
      ->where('is_active', true)
      ->get()
      ->toArray()
    ;

    return $users;
  }

}
