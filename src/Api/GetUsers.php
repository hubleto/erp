<?php declare(strict_types=1);

namespace Hubleto\Erp\Api;

use \Hubleto\App\Community\Settings\Models\User;

class GetUsers extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $mUser = $this->getModel(User::class);
    $users = $mUser->record
      ->select(['id', 'login', 'email', 'first_name', 'last_name', 'nick'])
      ->where('is_active', true)
      ->get()
      ->toArray()
    ;

    return $users;
  }

}
