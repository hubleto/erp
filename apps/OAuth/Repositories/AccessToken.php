<?php

namespace Hubleto\App\Community\OAuth\Repositories;

use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Hubleto\App\Community\OAuth\Entities\AccessTokenEntity;

class AccessToken extends \Hubleto\Framework\Core implements AccessTokenRepositoryInterface
{

  public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
  {
    $dbData = [
      'token_id' => $accessTokenEntity->getIdentifier(),
      'expires_at' => $accessTokenEntity->getExpiryDateTime(),
      'user_id' => $accessTokenEntity->getUserIdentifier(), // Can be null if no user is involved (e.g., client credentials)
      'client_id' => $accessTokenEntity->getClient()->getIdentifier(),
      'scopes' => json_encode(array_map(fn ($scope) => $scope->getIdentifier(), $accessTokenEntity->getScopes())),
      'code_challenge' => $accessTokenEntity->getCodeChallenge(),
      'code_challenge_method' => $accessTokenEntity->getCodeChallengeMethod(),
      // Add any other fields you need, like redirect_uri
      'redirect_uri' => $accessTokenEntity->getRedirectUri(), // Important for validating token exchange
      'revoked' => false,
    ];

    $accessTokenModel = $this->getModel(\Hubleto\App\Community\OAuth\Models\AccessToken::class);
    $accessTokenModel->record->recordCreate($dbData); // Assuming fillable properties
  }

  public function revokeAccessToken($tokenId): void
  {
    $accessTokenModel = $this->getModel(\Hubleto\App\Community\OAuth\Models\AccessToken::class);
    $accessTokenModel->record->where('access_token', $tokenId)->update(['revoked' => true]);
  }

  public function isAccessTokenRevoked($tokenId): bool
  {
    $accessTokenModel = $this->getModel(\Hubleto\App\Community\OAuth\Models\AccessToken::class);
    $accessToken = $accessTokenModel->record->find($tokenId);
    return $accessToken && $accessToken->revoked;
  }

  public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessTokenEntityInterface
  {
    $accessToken = new AccessTokenEntity();

    $accessToken->setClient($clientEntity);

    foreach ($scopes as $scope) {
      $accessToken->addScope($scope);
    }

    if ($userIdentifier !== null) {
      $accessToken->setUserIdentifier((string) $userIdentifier);
    }

    return $accessToken;
  }

}
