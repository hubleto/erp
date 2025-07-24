<?php

namespace HubletoApp\Community\OAuth\Repositories;

use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use HubletoApp\Community\OAuth\Entities\ScopeEntity;

class Scope implements ScopeRepositoryInterface
{
  public \HubletoMain\Loader $main;

  public function __construct(\HubletoMain\Loader $main)
  {
    $this->main = $main;
  }

  public function getScopeEntityByIdentifier($scopeIdentifier): ?ScopeEntityInterface
  {
    $scopes = [
      'basic' => [
        'description' => 'Basic details about you',
      ],
      'email' => [
        'description' => 'Your email address',
      ],
    ];

    if (array_key_exists($scopeIdentifier, $scopes) === false) {
      return null;
    }

    $scope = new ScopeEntity();
    $scope->setIdentifier($scopeIdentifier);

    return $scope;
  }

  public function finalizeScopes(
    array $scopes,
    $grantType,
    ClientEntityInterface $clientEntity,
    $userIdentifier = null,
    $authCodeId = null
  ): array {
    // Example of programatically modifying the final scope of the access token
    if ((int) $userIdentifier === 1) {
      $scope = new ScopeEntity();
      $scope->setIdentifier('email');
      $scopes[] = $scope;
    }

    return $scopes;
  }

}
