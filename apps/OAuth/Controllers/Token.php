<?php

namespace HubletoApp\Community\OAuth\Controllers;

class Token extends \HubletoApp\Community\OAuth\ServerController
{
  public bool $hideDefaultDesktop = true;
  public bool $requiresUserAuthentication = false;

  public function prepareView(): void
  {
    $server = $this->getServer();

    // --- Handling the Token Request (POST /oauth/token) ---
    // This typically happens in another controller/route.

    // Example: In your token endpoint (e.g., /oauth/token)
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], '/oauth/token') !== false) {
      try {
        $request = \Laminas\Diactoros\ServerRequestFactory::fromGlobals();
        $response = (new \Laminas\Diactoros\ResponseFactory())->createResponse();

        // This method handles all grant types added to the server, including AuthCodeGrant with PKCE.
        $response = $server->respondToAccessTokenRequest($request, $response);

        // Send the JSON response with access/refresh token
        foreach ($response->getHeaders() as $name => $values) {
          header(sprintf('%s: %s', $name, implode(', ', $values)), false);
        }
        echo (string) $response->getBody();

      } catch (\League\OAuth2\Server\Exception\OAuthServerException $exception) {
        $response = $exception->generateHttpResponse((new \Laminas\Diactoros\ResponseFactory())->createResponse());
        (new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
      } catch (\Exception $exception) {
        $response = (new \Laminas\Diactoros\ResponseFactory())->createResponse(500);
        $response->getBody()->write($exception->getMessage());
        $response->send();
      }
    }
  }

}
