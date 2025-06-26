<?php

namespace HubletoApp\Community\OAuth\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use OAuth2ServerExamples\Entities\ClientEntity;

class Client implements ClientRepositoryInterface {

  public \HubletoMain $main;

  public function __construct(\HubletoMain $main)
  {
    $this->main = $main;
  }

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