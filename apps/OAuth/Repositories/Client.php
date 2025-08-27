<?php

namespace HubletoApp\Community\OAuth\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use HubletoApp\Community\OAuth\Entities\ClientEntity;

class Client extends \Hubleto\Framework\Core implements ClientRepositoryInterface
{

  public function getClientEntity(string $clientIdentifier): ?ClientEntityInterface
  {

    $mClient = $this->getModel(\HubletoApp\Community\OAuth\Models\Client::class);
    $clientData = $mClient->record->where('client_id', $clientIdentifier)->first()?->toArray();

    $client = new ClientEntity();
    $client->setIdentifier($clientIdentifier);
    $client->setName($clientData['name'] ?? '');
    $client->setRedirectUri($clientData['redirect_uri'] ?? '');
    // $client->setConfidential();

    return $client;
  }

  public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
  {

    $mClient = $this->getModel(\HubletoApp\Community\OAuth\Models\Client::class);
    $clientData = $mClient->record->where('client_id', $clientIdentifier)->first()?->toArray();
    return ($clientData['client_secret'] ?? '') == $clientSecret;
  }
}
