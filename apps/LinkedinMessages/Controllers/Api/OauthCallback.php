<?php

namespace Hubleto\App\Community\LinkedinMessages\Controllers\Api;

use Hubleto\App\Community\LinkedinMessages\Client;
use Hubleto\App\Community\LinkedinMessages\Models\Account;

class OauthCallback extends \Hubleto\Erp\Controllers\ApiController
{
  public bool $hideDefaultDesktop = true;

  public function renderJson(): array
  {
    /** @var Client */
    $client = $this->getService(Client::class);
    /** @var Account */
    $mAccount = $this->getModel(Account::class);

    $fail = function (string $message) {
      $this->router()->redirectTo('linkedin-messages/settings?oauthError=' . urlencode($message));
      exit;
    };

    $error = $this->router()->urlParamAsString('error');
    if ($error !== '') {
      $fail($this->router()->urlParamAsString('error_description', $error));
    }

    if (session_status() !== PHP_SESSION_ACTIVE) @session_start();
    $expectedState = (string) ($_SESSION['linkedin-messages']['oauthState'] ?? '');
    $state = $this->router()->urlParamAsString('state');
    unset($_SESSION['linkedin-messages']['oauthState']);

    if ($expectedState === '' || !hash_equals($expectedState, $state)) {
      $fail($this->translate('Invalid OAuth state. Please try to connect again.'));
    }

    $code = $this->router()->urlParamAsString('code');
    if ($code === '') {
      $fail($this->translate('LinkedIn did not return an authorization code.'));
    }

    $token = $client->exchangeCodeForToken($code);
    if (empty($token['access_token'])) {
      $fail($this->translate('Could not obtain access token from LinkedIn.') . ' ' . (string) ($token['error_description'] ?? ($token['error'] ?? '')));
    }

    $accessToken = (string) $token['access_token'];
    $profile = [];
    if (str_contains((string) ($token['scope'] ?? $client->getScopes()), 'openid')) {
      $info = $client->fetchUserInfo($accessToken);
      if ($info['status'] == 200) $profile = $info['json'];
    }

    $idOwner = $this->authProvider()->getUserId();
    $memberId = (string) ($profile['sub'] ?? '');
    if ($memberId === '') $memberId = 'member-' . substr(sha1($accessToken), 0, 12);

    $data = [
      'id_owner' => $idOwner,
      'linkedin_member_id' => $memberId,
      'name' => (string) ($profile['name'] ?? trim(($profile['given_name'] ?? '') . ' ' . ($profile['family_name'] ?? ''))),
      'email' => (string) ($profile['email'] ?? ''),
      'picture_url' => (string) ($profile['picture'] ?? ''),
      'locale' => is_array($profile['locale'] ?? null)
        ? trim(($profile['locale']['language'] ?? '') . '_' . ($profile['locale']['country'] ?? ''), '_')
        : (string) ($profile['locale'] ?? ''),
      'access_token' => $accessToken,
      'refresh_token' => (string) ($token['refresh_token'] ?? ''),
      'token_expires_at' => date('Y-m-d H:i:s', time() + (int) ($token['expires_in'] ?? 3600)),
      'scopes' => (string) ($token['scope'] ?? $client->getScopes()),
      'status' => Account::STATUS_CONNECTED,
      'last_sync_info' => $this->translate('Connected. Synchronization will start shortly.'),
    ];
    if ($data['name'] === '') $data['name'] = $this->translate('LinkedIn account');

    $existing = $mAccount->record
      ->where('id_owner', $idOwner)
      ->where('linkedin_member_id', $memberId)
      ->first()
      ?->toArray()
    ;

    if ($existing) {
      $mAccount->record->where('id', $existing['id'])->update($data);
      $idAccount = (int) $existing['id'];
    } else {
      $idAccount = (int) $mAccount->record->recordCreate($data)['id'];
    }

    $this->router()->redirectTo('linkedin-messages/accounts/' . $idAccount);
    exit;
  }

}
