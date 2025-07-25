<?php

namespace HubletoApp\Community\OAuth;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\AuthCodeGrant;

class ServerController extends \HubletoMain\Controller
{
  public AuthorizationServer $server;

  public function getServer(): AuthorizationServer
  {
    $clientRepository = new \HubletoApp\Community\OAuth\Repositories\Client($this->main);
    $userRepository = new \HubletoApp\Community\OAuth\Repositories\User($this->main);
    $scopeRepository = new \HubletoApp\Community\OAuth\Repositories\Scope($this->main);
    $accessTokenRepository = new \HubletoApp\Community\OAuth\Repositories\AccessToken($this->main);
    $refreshTokenRepository = new \HubletoApp\Community\OAuth\Repositories\RefreshToken($this->main);
    $authCodeRepository = new \HubletoApp\Community\OAuth\Repositories\AuthCode($this->main);

    $privateKey = 'file://' . $this->main->config->getAsString('OAuthPrivateKey'); // Path to your private key for JWT signing
    $publicKey = 'file://' . $this->main->config->getAsString('OAuthPublicKey'); // Path to your public key for JWT validation

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
    // $passwordGrant->setAccessTokenTTL(new \DateInterval('PT1H')); // Access token will expire in 1 hour
    $server->enableGrantType($passwordGrant);

    // Enable the Authorization Code Grant
    $authCodeGrant = new AuthCodeGrant(
      $authCodeRepository,
      $refreshTokenRepository,
      new \DateInterval('PT10M') // Authorization codes will expire in 10 minutes
    );
    $server->enableGrantType($authCodeGrant);

    // Add the Refresh Token Grant (highly recommended with Authorization Code Grant)
    $refreshTokenGrant = new \League\OAuth2\Server\Grant\RefreshTokenGrant($refreshTokenRepository);
    $refreshTokenGrant->setRefreshTokenTTL(new \DateInterval('P1M')); // Refresh token expires in 1 month
    $server->enableGrantType($refreshTokenGrant);

    return $server;
  }

}
