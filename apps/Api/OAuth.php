<?php

namespace Hubleto\App\Community\Api;

class OAuth
{

  private string $tokenEndpoint;
  private string $clientId;
  private string $clientSecret;

  public function __construct(
    string $tokenEndpoint,
    string $clientId,
    string $clientSecret
  ) {
    $this->tokenEndpoint = $tokenEndpoint;
    $this->clientId = $clientId;
    $this->clientSecret = $clientSecret;
  }

  /**
   * [Description for obtainAccessToken]
   *
   * @return string
   * 
   */
  public function obtainAccessToken(): string
  {

    // $token = base64_encode($this->clientId . ':' . $this->clientSecret);
    $payload = http_build_query([
      'grant_type' => 'client_credentials',
      'client_id' => $this->clientId,
      'client_secret' => $this->clientSecret,
      // 'scope' => $scope
    ]);

    // build the curl request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->tokenEndpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/x-www-form-urlencoded',
      // "Authorization: Basic $token"
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // process and return the response
    $response = curl_exec($ch);
    $response = json_decode($response, true);

    if (
      !isset($response['access_token'])
      || !isset($response['token_type'])
    ) {
      throw new \Exception('Failed to obtain OAuth access token.');
    }

    // here's your token to use in API requests
    return $response['access_token'];
  }

  /**
   * [Description for decodeAccessToken]
   *
   * @param string $accessToken
   * 
   * @return array
   * 
   */
  public function decodeAccessToken(string $accessToken): array
  {
    $parts = explode('.', $accessToken);
    if (count($parts) !== 3) throw new \Exception('Invalid access token format.');

    // The payload is the second part, base64 URL-encoded
    $payload = $parts[1];
    
    // Convert base64url to standard base64
    $base64 = str_replace(['-', '_'], ['+', '/'], $payload);
    
    // Pad with trailing '=' if needed
    $decodedString = base64_decode(str_pad($base64, strlen($base64) % 4, '=', STR_PAD_RIGHT));
    
    $decoded = json_decode($decodedString, true);
    
    $isExpired = true;
    if (isset($decoded['exp'])) {
      $isExpired = time() >= $decoded['exp'];
    }

    if ($isExpired) throw new \Exception('Access token expired');
    return (is_array($decoded) ? $decoded : []);
  }

}