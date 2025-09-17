<?php

namespace Hubleto\App\Community\OAuth\Repositories;

use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Hubleto\App\Community\OAuth\Entities\UserEntity;

class User extends \Hubleto\Framework\Core implements UserRepositoryInterface
{

  public function getUserEntityByUserCredentials(
    $username,
    $password,
    $grantType,
    ClientEntityInterface $clientEntity
  ): ?UserEntityInterface {

    $mUser = $this->getService(\Hubleto\App\Community\Settings\User::class);

    $users = $mUser->record
      ->whereRaw("UPPER(email) LIKE ?", [ strtoupper(str_replace("'", "", $value)) ])
      ->where($this->activeAttribute, '<>', 0)
      ->get()
      ->makeVisible([$this->passwordAttribute])
      ->toArray()
    ;
    foreach ($users as $user) {
      if (password_verify($password, $user['password'] ?? '')) {
        return new UserEntity();
      }
    }


    return null;
  }

}
