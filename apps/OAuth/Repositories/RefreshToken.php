<?php

namespace HubletoApp\Community\OAuth\Repositories;

use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use HubletoApp\Community\OAuth\Entities\RefreshTokenEntity;

class RefreshToken implements RefreshTokenRepositoryInterface
{
  public \HubletoMain\Loader $main;

  public function __construct(\HubletoMain\Loader $main)
  {
    $this->main = $main;
  }

  public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
  {
    // Some logic to persist the refresh token in a database
  }

  public function revokeRefreshToken($tokenId): void
  {
    // Some logic to revoke the refresh token in a database
  }

  public function isRefreshTokenRevoked($tokenId): bool
  {
    return false; // The refresh token has not been revoked
  }

  public function getNewRefreshToken(): ?RefreshTokenEntityInterface
  {
    return new RefreshTokenEntity();
  }

}
