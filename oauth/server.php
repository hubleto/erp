<?php

require __DIR__ . '/vendor/autoload.php';

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\PasswordGrant;


use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use OAuth2ServerExamples\Entities\AccessTokenEntity;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use OAuth2ServerExamples\Entities\ClientEntity;

use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use OAuth2ServerExamples\Entities\UserEntity;

use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use OAuth2ServerExamples\Entities\RefreshTokenEntity;

use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use OAuth2ServerExamples\Entities\ScopeEntity;

use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use OAuth2ServerExamples\Entities\AuthCodeEntity;

// MyClientRepository
class MyClientRepository implements ClientRepositoryInterface {
  public function getClientEntity(string $clientIdentifier): ?ClientEntityInterface
  {
    $client = new ClientEntity();

    $client->setIdentifier($clientIdentifier);
    $client->setName(self::CLIENT_NAME);
    $client->setRedirectUri(self::REDIRECT_URI);
    $client->setConfidential();

    return $client;
  }

  public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
  {
    $clients = [
      'myawesomeapp' => [
        'secret'      => password_hash('abc123', PASSWORD_BCRYPT),
        'name'      => self::CLIENT_NAME,
        'redirect_uri'  => self::REDIRECT_URI,
        'is_confidential' => true,
      ],
    ];

    // Check if client is registered
    if (array_key_exists($clientIdentifier, $clients) === false) {
      return false;
    }

    if (password_verify($clientSecret, $clients[$clientIdentifier]['secret']) === false) {
      return false;
    }

    return true;
  }
}

// MyUserRepository
class MyUserRepository implements UserRepositoryInterface {
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

// MyScopeRepository
class MyScopeRepository implements ScopeRepositoryInterface {

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

// MyAccessTokenRepository
class MyAccessTokenRepository implements AccessTokenRepositoryInterface {

  public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
  {
    // Some logic here to save the access token to a database
  }

  public function revokeAccessToken($tokenId): void
  {
    // Some logic here to revoke the access token
  }

  public function isAccessTokenRevoked($tokenId): bool
  {
    return false; // Access token hasn't been revoked
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

// MyRefreshTokenRepository
class MyRefreshTokenRepository implements RefreshTokenRepositoryInterface {

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

// AuthCodeRepository
class AuthCodeRepository implements AuthCodeRepositoryInterface
{

  public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
  {
    // Some logic to persist the auth code to a database
  }

  public function revokeAuthCode($codeId): void
  {
    // Some logic to revoke the auth code in a database
  }

  public function isAuthCodeRevoked($codeId): bool
  {
    return false; // The auth code has not been revoked
  }

  public function getNewAuthCode(): AuthCodeEntityInterface
  {
    return new AuthCodeEntity();
  }

}




$clientRepository = new MyClientRepository();
$userRepository = new MyUserRepository();
$scopeRepository = new MyScopeRepository();
$accessTokenRepository = new MyAccessTokenRepository();
$refreshTokenRepository = new MyRefreshTokenRepository(); // Optional

$privateKey = 'file://' . __DIR__ . '/private.key'; // Path to your private key for JWT signing
$publicKey = 'file://' . __DIR__ . '/public.key';   // Path to your public key for JWT validation

// Setup the authorization server
$server = new AuthorizationServer(
  $clientRepository,
  $accessTokenRepository,
  $scopeRepository,
  $privateKey,
  $publicKey // Used for validating JWT access tokens by resource servers
);

// Enable the Resource Owner Password Credentials Grant
$passwordGrant = new PasswordGrant(
  $userRepository,
  $refreshTokenRepository // Pass null if you don't want refresh tokens for ROPC
);
$passwordGrant->setAccessTokenTTL(new \DateInterval('PT1H')); // Access token will expire in 1 hour
$server->addGrantType($passwordGrant);

// Enable the Refresh Token Grant (highly recommended if you use refresh tokens)
$refreshTokenGrant = new \League\OAuth2\Server\Grant\RefreshTokenGrant($refreshTokenRepository);
$refreshTokenGrant->setRefreshTokenTTL(new \DateInterval('P1M')); // Refresh token will expire in 1 month
$server->addGrantType($refreshTokenGrant);


// Handle the request
try {
  $request = ServerRequestFactory::fromGlobals();
  $response = (new ResponseFactory())->createResponse();

  $response = $server->respondToAccessTokenRequest($request, $response);

  // Send the response
  foreach ($response->getHeaders() as $name => $values) {
    header(sprintf('%s: %s', $name, implode(', ', $values)), false);
  }
  echo (string) $response->getBody();

} catch (\League\OAuth2\Server\Exception\OAuthServerException $exception) {
  // All OAuth 2.0 errors come from here
  $exception->generateHttpResponse((new ResponseFactory())->createResponse())->send();
} catch (\Exception $exception) {
  // Any other error (general system error)
  $response = (new ResponseFactory())->createResponse(500);
  $response->getBody()->write($exception->getMessage());
  $response->send();
}