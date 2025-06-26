<?php

namespace HubletoApp\Community\OAuth\Repositories;

use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use HubletoApp\Community\OAuth\Entities\UserEntity;

class User implements UserRepositoryInterface {

  public \HubletoMain $main;

  public function __construct(\HubletoMain $main)
  {
    $this->main = $main;
  }

  public function getUserEntityByUserCredentials(
    $username,
    $password,
    $grantType,
    ClientEntityInterface $clientEntity
  ): ?UserEntityInterface {

    $mUser = new \HubletoApp\Community\Settings\User($this->main);

    $users = $mUser->record
      ->whereRaw("UPPER(email) LIKE '" . strtoupper(str_replace("'", "", $value)))
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