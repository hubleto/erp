<?php

namespace HubletoMain\Cli\Agent\Debug;

class MyProvider extends \League\OAuth2\Client\Provider\GenericProvider {
  private $pkceMethod = 'S256';

  protected function getPkceMethod() { return 'S256'; }
  protected function getAccessTokenMethod() { return self::METHOD_GET; }
}

class TestOauth extends \HubletoMain\Cli\Agent\Command
{

  public function run(): void
  {
    $clientId = (string) ($this->arguments[3] ?? '');
    $clientSecret = (string) ($this->arguments[4] ?? '');
    $serverBaseUrl = (string) ($this->arguments[5] ?? '');
    $redirectUri = (string) ($this->arguments[6] ?? '');

    if (empty($clientId) || empty($clientSecret) || empty($serverBaseUrl) || empty($redirectUri)) {
      $this->cli->white("Usage:\n");
      $this->cli->white("  php hubleto debug test-oauth <clientId> <clientSecret> <serverBaseUrl> <redirectUri>\n");
    }

    $provider = new MyProvider([
      'clientId'                => $clientId,
      'clientSecret'            => $clientSecret,
      'redirectUri'             => $redirectUri,
      'urlAuthorize'            => $serverBaseUrl . '/authorize',
      'urlAccessToken'          => $serverBaseUrl . '/token',
      'urlResourceOwnerDetails' => $serverBaseUrl . '/resource',
    ]);

    $authorizationUrl = $provider->getAuthorizationUrl();
    // $accessTokenUrl = $provider->getAccessTokenUrl();

    $oauth2state = $provider->getState();
    $oauth2pkceCode = $provider->getPkceCode();

    // Create a client and send the request
    // $request = (new \Laminas\Http\Request())->withUri(new \Laminas\Diactoros\Uri($authorizationUrl));
    $response = (new \Laminas\Http\Client($authorizationUrl, [ 'maxredirects' => 0 ]))->send();

    $statusCode = $response->getStatusCode();
    $redirectLocation = str_replace("Location: ", "", (string) $response->getHeaders()->get('location'));
    $uri = \Laminas\Uri\UriFactory::factory($redirectLocation);
    $scheme = $uri->getQueryAsArray();

// var_dump($oauth2pkceCode);
// var_dump($redirectLocation);
// var_dump($scheme);
// var_dump((string) $response->getBody());
    if (isset($scheme['error'])) {
      $this->cli->red("Error\n");
      $this->cli->red($scheme['error'] . "\n");
      $this->cli->red($scheme['hint'] . "\n");
    } else {
      // $response = (new \Laminas\Http\Client($accessTokenUrl, [ 'maxredirects' => 0 ]))->send();
      $provider->getAccessToken('authorization_code', ['code' => $scheme['code']]);
    }
  }
}