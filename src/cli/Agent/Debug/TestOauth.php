<?php

namespace HubletoMain\Cli\Agent\Debug;

class TestOauth extends \HubletoMain\Cli\Agent\Command
{

  public function run(): void
  {
    $clientId = (string) ($this->arguments[3] ?? '');
    $clientSecret = (string) ($this->arguments[4] ?? '');
    $serverBaseUrl = (string) ($this->arguments[5] ?? '');

    if (empty($clientId) || empty($clientSecret) || empty($serverBaseUrl)) {
      $this->cli->white("Usage:\n");
      $this->cli->white("  php hubleto debug test-oauth <clientId> <clientSecret> <serverBaseUrl>\n");
    }

    $provider = new \League\OAuth2\Client\Provider\GenericProvider([
      'clientId'                => $clientId,
      'clientSecret'            => $clientSecret,
      // 'redirectUri'             => 'https://my.example.com/your-redirect-url/',
      'urlAuthorize'            => $serverBaseUrl . '/authorize',
      'urlAccessToken'          => $serverBaseUrl . '/token',
      'urlResourceOwnerDetails' => $serverBaseUrl . '/resource',
    ]);

    $authorizationUrl = $provider->getAuthorizationUrl();

    $_SESSION['oauth2state'] = $provider->getState();
    $_SESSION['oauth2pkceCode'] = $provider->getPkceCode();

    echo $authorizationUrl;

  }
}