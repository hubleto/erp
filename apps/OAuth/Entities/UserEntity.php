<?php

namespace HubletoApp\Community\OAuth\Entities;

use League\OAuth2\Server\Entities\UserEntityInterface;

class UserEntity implements UserEntityInterface
{
  /**
   * Return the user's identifier.
   */
  public function getIdentifier(): string
  {
    return '1';
  }
}
