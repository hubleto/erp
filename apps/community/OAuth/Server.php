<?php

require __DIR__ . '/vendor/autoload.php';

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\PasswordGrant;

$clientRepository = new Repositories\Client();
$userRepository = new Repositories\User();
$scopeRepository = new Repositories\Scope();
$accessTokenRepository = new Repositories\AccessToken();
$refreshTokenRepository = new Repositories\RefreshToken();
$authCodeRepository = new Repositories\AuthCode();

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

// Enable the Authorization Code Grant
$authCodeGrant = new AuthCodeGrant(
  $authCodeRepository,
  $userRepository, // Provide your user repository for user authentication
  new \DateInterval('PT10M') // Authorization codes will expire in 10 minutes
);

// PKCE is automatically enabled when a client provides code_challenge parameters.
// However, you can enforce PKCE for public clients:
$authCodeGrant->setRequireCodeChallengeForPublicClients(true); // Recommended for security

$server->addGrantType($authCodeGrant);

// Add the Refresh Token Grant (highly recommended with Authorization Code Grant)
$refreshTokenGrant = new \League\OAuth2\Server\Grant\RefreshTokenGrant($refreshTokenRepository);
$refreshTokenGrant->setRefreshTokenTTL(new \DateInterval('P1M')); // Refresh token expires in 1 month
$server->addGrantType($refreshTokenGrant);







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