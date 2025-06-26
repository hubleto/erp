<?php

namespace HubletoApp\Community\OAuth\Repositories;

use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use OAuth2ServerExamples\Entities\UserEntity;

// MyUserRepository
class MyUserRepository implements UserRepositoryInterface {

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
    if ($username === 'alex' && $password === 'whisky') {
      return new UserEntity();
    }

    return null;
  }

}