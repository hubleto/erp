<?php declare(strict_types=1);

namespace Hubleto\Erp\Api;

use \Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Helper;

class GetUsers extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $mUser = $this->getModel(User::class);
    $users = Helper::keyBy('id', $mUser->record
      ->select(['id', 'login', 'email', 'first_name', 'last_name', 'nick', 'photo', 'position'])
      ->with('TEAMS')
      ->with('ROLES')
      ->where('is_active', true)
      ->get()
      ->toArray()
    );

    $authProvider = $this->authProvider();
    
    foreach ($users as $idUser => $user) {
      $hasAllGranted = false;
      foreach ($user['ROLES'] as $role) {
        if ($role['grant_all']) $hasAllGranted = true;
      }
      $users[$idUser]['HAS_ALL_GRANTED'] = $hasAllGranted;

      if ($idUser == $authProvider->getUserId()) {
        $users[$idUser]['SIGNED_IN'] = true;
      }
    }

    return $users;
  }

}
