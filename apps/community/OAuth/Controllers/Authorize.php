<?php

namespace HubletoApp\Community\OAuth\Controllers;

class Authorize extends \HubletoApp\Community\OAuth\ServerController
{
  public bool $hideDefaultDesktop = true;
  public bool $requiresUserAuthentication = false;

  public function render(array $params): string
  {
    $server = $this->getServer();

    // --- Handling the Authorization Request (GET /oauth/authorize) ---
    // This is where the user interacts with your login/consent page.
    // This typically happens in a separate controller/route.

    // Example: In your authorization endpoint (e.g., /oauth/authorize)
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], '/oauth/authorize') !== false) {
      try {

        $request = \Laminas\Diactoros\ServerRequestFactory::fromGlobals();
        $response = (new \Laminas\Diactoros\ResponseFactory())->createResponse();

        // Validate the HTTP request and return an AuthorizationRequest object.
        $authRequest = $server->validateAuthorizationRequest($request);

        // Your application's logic to authenticate the user and get their consent.
        // If the user is not logged in, redirect them to your login page.
        // After successful login, you'd get the user ID.
        // Assume $authenticatedUserId and $userConsentApproved are set after your login/consent process.

        $authenticatedUserId = '123'; // Replace with actual authenticated user ID
        $userConsentApproved = true; // Replace with actual user consent (from a form, for example)

        // Set the user on the authorization request.
        $authRequest->setUser(new \HubletoApp\Community\OAuth\Entities\UserEntity($authenticatedUserId)); // Your user entity

        // Set the final scopes that were approved by the user
        $authRequest->setScopes($authRequest->getScopes()); // You might filter/reduce these based on consent

        $authRequest->setAuthorizationApproved(true);

        if ($userConsentApproved === true) {
          // Once the user approves the authorization, complete the flow.
          $response = $server->completeAuthorizationRequest($authRequest, $response);
        } else {
          // User denied the consent
          throw \League\OAuth2\Server\Exception\OAuthServerException::accessDenied($authRequest->getRedirectUri());
        }

        // Send the response (redirect to client with code)
        foreach ($response->getHeaders() as $name => $values) {
          header(sprintf('%s: %s', $name, implode(', ', $values)), false);
        }
        echo (string) $response->getBody();

      } catch (\League\OAuth2\Server\Exception\OAuthServerException $exception) {
        $response = $exception->generateHttpResponse((new \Laminas\Diactoros\ResponseFactory())->createResponse());
        (new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
      } catch (\Exception $exception) {
        // Handle other general errors
        $response = (new \Laminas\Diactoros\ResponseFactory())->createResponse(500);
        $response->getBody()->write($exception->getMessage());
        (new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
      }
    }

    exit;
  }

}
