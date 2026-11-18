<?php declare(strict_types=1);

namespace Hubleto\Erp\Api;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Helper;

class GetUsers extends \Hubleto\Erp\Controllers\ApiController
{
  public bool $permittedForAllUsers = true;

  public function renderJson(): array
  {
    // Inactive users are needed e.g. by InputMultipleUsers, which shows them while they are selected.
    $includeInactive = $this->router()->urlParamAsBool('includeInactive');

    $mUser = $this->getModel(User::class);
    $query = $mUser->record
      ->select(['id', 'login', 'email', 'first_name', 'last_name', 'nick', 'photo', 'position', 'is_active'])
      ->with('TEAMS')
      ->with('ROLES')
    ;
    if (!$includeInactive) {
      $query = $query->where('is_active', true);
    }
    $users = Helper::keyBy('id', $query->get()->toArray());

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
