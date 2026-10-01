<?php

namespace Hubleto\App\Custom\LinkedinMessages;

use Hubleto\Erp\Core;

/**
 * Thin client for the LinkedIn APIs used by this app.
 *
 * IMPORTANT - what LinkedIn actually allows:
 *  - OAuth sign-in + own profile (OpenID Connect `userinfo`) is available to any LinkedIn app.
 *  - Reading messages is possible only through the Member Data Portability API (DMA):
 *    `memberChangeLogs` (last 28 days, new events) and `memberSnapshotData?domain=INBOX` (history).
 *    These need the `r_dma_portability_*` scope and the Data Portability product on your LinkedIn app.
 *  - Sending messages (`POST /v2/messages`) is restricted to LinkedIn-approved partners (scope `w_messages`).
 *    Without partner access LinkedIn answers 403 and the app falls back to the manual-send flow.
 */
class Client extends Core
{
  public const AUTH_URL = 'https://www.linkedin.com/oauth/v2/authorization';
  public const TOKEN_URL = 'https://www.linkedin.com/oauth/v2/accessToken';
  public const API_BASE = 'https://api.linkedin.com';
  public const DEFAULT_SCOPES = 'openid profile email r_dma_portability_self_serve';
  public const DEFAULT_API_VERSION = '202312';

  public function getApp(): Loader
  {
    /** @var Loader */
    $app = $this->appManager()->getApp(Loader::class);
    return $app;
  }

  public function getConfig(string $key, string $default = ''): string
  {
    $value = trim($this->getApp()->configAsString($key));
    return $value !== '' ? $value : $default;
  }

  public function isConfigured(): bool
  {
    return $this->getConfig('clientId') !== '' && $this->getConfig('clientSecret') !== '';
  }

  public function getRedirectUri(): string
  {
    return rtrim($this->env()->projectUrl, '/') . '/linkedin-messages/api/oauth-callback';
  }

  public function getScopes(): string
  {
    return $this->getConfig('scopes', self::DEFAULT_SCOPES);
  }

  public function buildAuthorizationUrl(string $state): string
  {
    return self::AUTH_URL . '?' . http_build_query([
      'response_type' => 'code',
      'client_id' => $this->getConfig('clientId'),
      'redirect_uri' => $this->getRedirectUri(),
      'state' => $state,
      'scope' => $this->getScopes(),
    ]);
  }

  /** @return array{access_token?:string,expires_in?:int,refresh_token?:string,scope?:string,error?:string,error_description?:string} */
  public function exchangeCodeForToken(string $code): array
  {
    return $this->postForm(self::TOKEN_URL, [
      'grant_type' => 'authorization_code',
      'code' => $code,
      'client_id' => $this->getConfig('clientId'),
      'client_secret' => $this->getConfig('clientSecret'),
      'redirect_uri' => $this->getRedirectUri(),
    ])['json'];
  }

  public function refreshAccessToken(string $refreshToken): array
  {
    return $this->postForm(self::TOKEN_URL, [
      'grant_type' => 'refresh_token',
      'refresh_token' => $refreshToken,
      'client_id' => $this->getConfig('clientId'),
      'client_secret' => $this->getConfig('clientSecret'),
    ])['json'];
  }

  /** OpenID Connect userinfo: sub, name, given_name, family_name, picture, email, locale */
  public function fetchUserInfo(string $accessToken): array
  {
    return $this->request('GET', self::API_BASE . '/v2/userinfo', $accessToken);
  }

  /** Member Data Portability: changelog events (new messages since $startTimeMs). */
  public function fetchChangelogPage(string $accessToken, int $startTimeMs, int $start = 0, int $count = 50): array
  {
    $query = ['q' => 'memberAndApplication', 'count' => $count, 'start' => $start];
    if ($startTimeMs > 0) $query['startTime'] = $startTimeMs;
    return $this->request('GET', self::API_BASE . '/rest/memberChangeLogs?' . http_build_query($query), $accessToken, null, true);
  }

  /** Member Data Portability: snapshot of the inbox (history). */
  public function fetchInboxSnapshotPage(string $accessToken, int $start = 0, int $count = 50): array
  {
    $query = ['q' => 'criteria', 'domain' => 'INBOX', 'start' => $start, 'count' => $count];
    return $this->request('GET', self::API_BASE . '/rest/memberSnapshotData?' . http_build_query($query), $accessToken, null, true);
  }

  /**
   * Sends a message. Partner-only API (scope w_messages). Plain text only, no HTML.
   *
   * @param array $recipientUrns e.g. ['urn:li:person:abc']
   * @param string $threadUrn optional, to reply into an existing conversation
   */
  public function sendMessage(string $accessToken, array $recipientUrns, string $subject, string $body, string $threadUrn = ''): array
  {
    $payload = [
      'subject' => $subject,
      'body' => strip_tags($body),
    ];
    if ($threadUrn !== '') {
      $payload['thread'] = $threadUrn;
    } else {
      $payload['recipients'] = array_values($recipientUrns);
    }
    return $this->request('POST', self::API_BASE . '/v2/messages', $accessToken, json_encode($payload));
  }

  /**
   * @return array{status:int,json:array,raw:string,error:string}
   */
  public function request(string $method, string $url, string $accessToken, ?string $jsonBody = null, bool $versioned = false): array
  {
    $headers = [
      'Authorization: Bearer ' . $accessToken,
      'X-Restli-Protocol-Version: 2.0.0',
      'Accept: application/json',
    ];
    if ($versioned) {
      $headers[] = 'LinkedIn-Version: ' . $this->getConfig('apiVersion', self::DEFAULT_API_VERSION);
    }
    if ($jsonBody !== null) {
      $headers[] = 'Content-Type: application/json';
    }
    return $this->http($method, $url, $headers, $jsonBody);
  }

  public function postForm(string $url, array $fields): array
  {
    return $this->http('POST', $url, ['Content-Type: application/x-www-form-urlencoded', 'Accept: application/json'], http_build_query($fields));
  }

  /**
   * @return array{status:int,json:array,raw:string,error:string}
   */
  protected function http(string $method, string $url, array $headers, ?string $body): array
  {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_CUSTOMREQUEST => $method,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_FOLLOWLOCATION => false,
    ]);
    if ($body !== null) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }

    $raw = curl_exec($ch);
    $error = $raw === false ? curl_error($ch) : '';
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    $json = [];
    if (is_string($raw) && $raw !== '') {
      $decoded = json_decode($raw, true);
      if (is_array($decoded)) $json = $decoded;
    }

    return [
      'status' => $status,
      'json' => $json,
      'raw' => is_string($raw) ? $raw : '',
      'error' => $error,
    ];
  }

  /** Human readable description of a failed API response. */
  public function describeError(array $response): string
  {
    if ($response['error'] !== '') return $response['error'];
    $json = $response['json'];
    $message = $json['message'] ?? ($json['error_description'] ?? ($json['error'] ?? ''));
    return 'HTTP ' . $response['status'] . ($message !== '' ? ': ' . (is_string($message) ? $message : json_encode($message)) : '');
  }
}
