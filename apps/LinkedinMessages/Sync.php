<?php

namespace Hubleto\App\Custom\LinkedinMessages;

use Hubleto\Erp\Core;
use Hubleto\App\Custom\LinkedinMessages\Models\Account;
use Hubleto\App\Custom\LinkedinMessages\Models\Message;
use Hubleto\App\Custom\LinkedinMessages\Models\Profile;

/**
 * Pulls messages and sender profiles from LinkedIn and stores them in the app's own tables.
 */
class Sync extends Core
{
  public const MAX_PAGES = 20;
  public const PAGE_SIZE = 50;

  /**
   * Synchronizes all connected accounts.
   *
   * @return array<int,array>
   */
  public function syncAll(): array
  {
    /** @var Account $mAccount */
    $mAccount = $this->getModel(Account::class);
    $accounts = $mAccount->record->where('status', '!=', Account::STATUS_ERROR)->whereNotNull('access_token')->get()?->toArray() ?? [];

    $results = [];
    foreach ($accounts as $account) {
      $results[(int) $account['id']] = $this->syncAccount((int) $account['id']);
    }
    return $results;
  }

  /**
   * @return array{ok:bool,created:int,updated:int,profiles:int,info:string}
   */
  public function syncAccount(int $idAccount): array
  {
    /** @var Client $client */
    $client = $this->getService(Client::class);
    /** @var Account $mAccount */
    $mAccount = $this->getModel(Account::class);

    $account = $mAccount->record->where('id', $idAccount)->first()?->toArray();
    $result = ['ok' => false, 'created' => 0, 'updated' => 0, 'profiles' => 0, 'info' => ''];

    if (!$account) {
      $result['info'] = $this->translate('Account not found.');
      return $result;
    }

    $token = $this->ensureValidToken($account);
    if ($token === '') {
      $result['info'] = $this->translate('The access token expired. Please reconnect the LinkedIn account.');
      $this->saveSyncResult($account, $result, Account::STATUS_EXPIRED);
      return $result;
    }

    $notes = [];

    // 1) history (once): inbox snapshot
    if (empty($account['changelog_cursor'])) {
      $snapshot = $this->importInboxSnapshot($client, $account, $token);
      $result['created'] += $snapshot['created'];
      $result['profiles'] += $snapshot['profiles'];
      if ($snapshot['info'] !== '') $notes[] = $snapshot['info'];
    }

    // 2) new events: changelog
    $changelog = $this->importChangelog($client, $account, $token);
    $result['created'] += $changelog['created'];
    $result['updated'] += $changelog['updated'];
    $result['profiles'] += $changelog['profiles'];
    if ($changelog['info'] !== '') $notes[] = $changelog['info'];

    $result['ok'] = $changelog['ok'];
    $result['info'] = trim(implode(' ', $notes));
    if ($result['info'] === '') {
      $result['info'] = sprintf($this->translate('Imported %d new messages.'), $result['created']);
    }

    $this->saveSyncResult(
      $account,
      $result,
      $result['ok'] ? Account::STATUS_CONNECTED : Account::STATUS_ERROR,
      $changelog['cursor'] ?? null
    );

    return $result;
  }

  /**
   * Returns a usable access token (refreshing it when needed) or an empty string.
   */
  public function ensureValidToken(array &$account): string
  {
    $token = (string) ($account['access_token'] ?? '');
    if ($token === '') return '';

    $expiresAt = !empty($account['token_expires_at']) ? strtotime($account['token_expires_at']) : 0;
    if ($expiresAt === 0 || $expiresAt > time() + 300) return $token;

    $refreshToken = (string) ($account['refresh_token'] ?? '');
    if ($refreshToken === '') return '';

    /** @var Client $client */
    $client = $this->getService(Client::class);
    $new = $client->refreshAccessToken($refreshToken);
    if (empty($new['access_token'])) return '';

    $update = [
      'access_token' => $new['access_token'],
      'token_expires_at' => date('Y-m-d H:i:s', time() + (int) ($new['expires_in'] ?? 3600)),
    ];
    if (!empty($new['refresh_token'])) $update['refresh_token'] = $new['refresh_token'];

    /** @var Account $mAccount */
    $mAccount = $this->getModel(Account::class);
    $mAccount->record->where('id', $account['id'])->update($update);
    $account = array_merge($account, $update);

    return (string) $new['access_token'];
  }

  protected function saveSyncResult(array $account, array $result, int $status, ?string $cursor = null): void
  {
    /** @var Account $mAccount */
    $mAccount = $this->getModel(Account::class);
    $update = [
      'status' => $status,
      'last_sync_at' => date('Y-m-d H:i:s'),
      'last_sync_info' => mb_substr((string) $result['info'], 0, 2000),
    ];
    if ($cursor !== null && $cursor !== '') $update['changelog_cursor'] = $cursor;
    $mAccount->record->where('id', $account['id'])->update($update);
  }

  // ---------------------------------------------------------------- changelog

  /**
   * @return array{ok:bool,created:int,updated:int,profiles:int,info:string,cursor:?string}
   */
  protected function importChangelog(Client $client, array $account, string $token): array
  {
    $out = ['ok' => true, 'created' => 0, 'updated' => 0, 'profiles' => 0, 'info' => '', 'cursor' => null];

    $startTime = (int) ($account['changelog_cursor'] ?? 0);
    // LinkedIn keeps only 28 days of changelog events
    $oldest = (time() - 27 * 86400) * 1000;
    if ($startTime < $oldest) $startTime = $oldest;

    $maxProcessedAt = (int) ($account['changelog_cursor'] ?? 0);

    for ($page = 0; $page < self::MAX_PAGES; $page++) {
      $response = $client->fetchChangelogPage($token, $startTime, $page * self::PAGE_SIZE, self::PAGE_SIZE);

      if ($response['status'] >= 400 || $response['error'] !== '') {
        $out['ok'] = false;
        $out['info'] = $this->translate('Cannot read messages from LinkedIn.') . ' ' . $client->describeError($response);
        if ($response['status'] == 401) $out['info'] .= ' ' . $this->translate('Please reconnect the account.');
        if ($response['status'] == 403) $out['info'] .= ' ' . $this->translate('Your LinkedIn app probably does not have the Member Data Portability product enabled.');
        return $out;
      }

      $elements = $response['json']['elements'] ?? [];
      if (!is_array($elements) || count($elements) == 0) break;

      foreach ($elements as $element) {
        $processedAt = (int) ($element['processedAt'] ?? ($element['capturedAt'] ?? 0));
        if ($processedAt > $maxProcessedAt) $maxProcessedAt = $processedAt;

        if (strtolower((string) ($element['resourceName'] ?? '')) !== 'messages') continue;

        $stored = $this->storeChangelogMessage($account, $element);
        if ($stored['created']) $out['created']++;
        if ($stored['updated']) $out['updated']++;
        if ($stored['profileCreated']) $out['profiles']++;
      }

      if (count($elements) < self::PAGE_SIZE) break;
    }

    if ($maxProcessedAt > 0) $out['cursor'] = (string) $maxProcessedAt;
    // after the first run mark the cursor so that the snapshot import is not repeated
    if ($out['cursor'] === null) $out['cursor'] = (string) (time() * 1000);

    return $out;
  }

  /**
   * @return array{created:bool,updated:bool,profileCreated:bool}
   */
  protected function storeChangelogMessage(array $account, array $element): array
  {
    $res = ['created' => false, 'updated' => false, 'profileCreated' => false];

    $activity = $element['activity'] ?? [];
    $processed = $element['processedActivity'] ?? [];
    if (!is_array($activity)) return $res;

    $messageId = (string) ($activity['id'] ?? ($element['resourceId'] ?? ($element['id'] ?? '')));
    if ($messageId === '') return $res;

    $authorUrn = (string) ($activity['author'] ?? ($element['actor'] ?? ''));
    $ownerUrn = (string) ($element['owner'] ?? '');
    $isOutgoing = $this->isOwnUrn($account, $authorUrn, $ownerUrn);

    $text = $activity['content']['content']['string']
      ?? ($activity['content']['string']
      ?? ($activity['content']['fallback']
      ?? ($activity['body'] ?? '')));

    $sentAtMs = (int) ($activity['createdAt'] ?? ($activity['deliveredAt'] ?? ($element['capturedAt'] ?? 0)));
    $sentAt = $sentAtMs > 0 ? date('Y-m-d H:i:s', (int) ($sentAtMs / 1000)) : date('Y-m-d H:i:s');
    $isRead = $isOutgoing || !empty($activity['readAt']);

    // counterpart: the author for incoming messages, the other member of the thread for outgoing
    $profileId = null;
    if (!$isOutgoing && $authorUrn !== '') {
      $decorated = $processed['author~'] ?? ($element['actor~'] ?? []);
      $p = $this->findOrCreateProfile($account, $authorUrn, is_array($decorated) ? $this->profileDataFromDecorated($decorated) : []);
      $profileId = $p['id'];
      $res['profileCreated'] = $p['created'];
    } elseif ($isOutgoing) {
      foreach (($processed['thread~']['membership'] ?? []) as $membership) {
        $urn = (string) ($membership['identity'] ?? '');
        if ($urn === '' || $this->isOwnUrn($account, $urn, $ownerUrn)) continue;
        $decorated = $membership['identity~']['member~'] ?? [];
        $p = $this->findOrCreateProfile($account, $urn, is_array($decorated) ? $this->profileDataFromDecorated($decorated) : []);
        $profileId = $p['id'];
        $res['profileCreated'] = $p['created'];
        break;
      }
    }

    $saved = $this->saveMessage($account, [
      'linkedin_message_id' => $messageId,
      'thread_id' => (string) ($activity['thread'] ?? ''),
      'direction' => $isOutgoing ? Message::DIRECTION_OUTGOING : Message::DIRECTION_INCOMING,
      'subject' => (string) ($activity['subject'] ?? ''),
      'body' => (string) $text,
      'sent_at' => $sentAt,
      'is_read' => $isRead ? 1 : 0,
      'id_profile' => $profileId,
    ]);

    $res['created'] = $saved === 'created';
    $res['updated'] = $saved === 'updated';

    return $res;
  }

  // ----------------------------------------------------------------- snapshot

  /**
   * Historical import from the INBOX snapshot domain. The snapshot columns mirror LinkedIn's data
   * export (CONVERSATION ID, FROM, SENDER PROFILE URL, TO, RECIPIENT PROFILE URLS, DATE, SUBJECT, CONTENT, FOLDER),
   * but the exact names are matched case-insensitively and missing columns are tolerated.
   *
   * @return array{created:int,profiles:int,info:string}
   */
  protected function importInboxSnapshot(Client $client, array $account, string $token): array
  {
    $out = ['created' => 0, 'profiles' => 0, 'info' => ''];

    for ($page = 0; $page < self::MAX_PAGES; $page++) {
      $response = $client->fetchInboxSnapshotPage($token, $page * self::PAGE_SIZE, self::PAGE_SIZE);

      if ($response['status'] >= 400 || $response['error'] !== '') {
        $out['info'] = $this->translate('Inbox history could not be imported.') . ' ' . $client->describeError($response);
        return $out;
      }

      $elements = $response['json']['elements'] ?? [];
      if (!is_array($elements) || count($elements) == 0) break;

      $rows = 0;
      foreach ($elements as $element) {
        foreach (($element['snapshotData'] ?? []) as $row) {
          if (!is_array($row)) continue;
          $rows++;
          $stored = $this->storeSnapshotRow($account, $row);
          if ($stored['created']) $out['created']++;
          if ($stored['profileCreated']) $out['profiles']++;
        }
      }

      if ($rows == 0 || count($elements) < self::PAGE_SIZE) break;
    }

    return $out;
  }

  /**
   * @return array{created:bool,profileCreated:bool}
   */
  protected function storeSnapshotRow(array $account, array $row): array
  {
    $res = ['created' => false, 'profileCreated' => false];

    $r = [];
    foreach ($row as $k => $v) {
      $r[strtolower(trim(str_replace('_', ' ', (string) $k)))] = is_scalar($v) ? (string) $v : '';
    }

    $content = $r['content'] ?? ($r['body'] ?? '');
    $from = $r['from'] ?? '';
    $senderUrl = $r['sender profile url'] ?? '';
    $to = $r['to'] ?? '';
    $recipientUrls = $r['recipient profile urls'] ?? '';
    $date = $r['date'] ?? '';
    $thread = $r['conversation id'] ?? '';
    $folder = strtoupper($r['folder'] ?? '');

    if ($content === '' && $from === '' && $date === '') return $res;

    $isOutgoing = $folder === 'SENT'
      || ($from !== '' && !empty($account['name']) && mb_strtolower($from) === mb_strtolower($account['name']))
      || ($senderUrl !== '' && !empty($account['profile_url']) && rtrim($senderUrl, '/') === rtrim($account['profile_url'], '/'));

    if ($isOutgoing) {
      $name = $to;
      $url = trim(explode(',', $recipientUrls)[0] ?? '');
    } else {
      $name = $from;
      $url = $senderUrl;
    }

    $profileId = null;
    if ($url !== '' || $name !== '') {
      $parts = preg_split('/\s+/', trim($name), 2);
      $p = $this->findOrCreateProfile($account, $url !== '' ? $url : 'name:' . mb_strtolower($name), [
        'first_name' => $parts[0] ?? '',
        'last_name' => $parts[1] ?? '',
        'profile_url' => str_starts_with($url, 'http') ? $url : '',
      ]);
      $profileId = $p['id'];
      $res['profileCreated'] = $p['created'];
    }

    $ts = strtotime($date);
    $sentAt = $ts ? date('Y-m-d H:i:s', $ts) : date('Y-m-d H:i:s');

    $saved = $this->saveMessage($account, [
      'linkedin_message_id' => 'snap-' . md5($thread . '|' . $date . '|' . $from . '|' . $content),
      'thread_id' => $thread,
      'direction' => $isOutgoing ? Message::DIRECTION_OUTGOING : Message::DIRECTION_INCOMING,
      'subject' => $r['subject'] ?? '',
      'body' => $content,
      // historical messages are not counted as unread
      'is_read' => 1,
      'sent_at' => $sentAt,
      'id_profile' => $profileId,
    ]);

    $res['created'] = $saved === 'created';
    return $res;
  }

  /**
   * Stores a message sent by the user (via API or manually) into the same conversation.
   *
   * @param array $message The message that is being answered.
   *
   * @return int ID of the created outgoing message.
   */
  public function storeOutgoingMessage(array $message, string $body): int
  {
    /** @var Message $mMessage */
    $mMessage = $this->getModel(Message::class);

    $created = $mMessage->record->recordCreate([
      'id_account' => $message['id_account'] ?? null,
      'id_profile' => $message['id_profile'] ?? null,
      'id_campaign' => $message['id_campaign'] ?? null,
      'id_owner' => $this->authProvider()->getUserId() ?: ($message['id_owner'] ?? null),
      'linkedin_message_id' => 'local-' . bin2hex(random_bytes(8)),
      'thread_id' => (string) ($message['thread_id'] ?? ''),
      'direction' => Message::DIRECTION_OUTGOING,
      'subject' => (string) ($message['subject'] ?? ''),
      'body' => $body,
      'sent_at' => date('Y-m-d H:i:s'),
      'is_read' => 1,
      'date_created' => date('Y-m-d H:i:s'),
    ]);

    return (int) $created['id'];
  }

  // ------------------------------------------------------------------ helpers

  /**
   * @return string 'created'|'updated'|'exists'
   */
  protected function saveMessage(array $account, array $data): string
  {
    /** @var Message $mMessage */
    $mMessage = $this->getModel(Message::class);

    $existing = $mMessage->record
      ->where('id_account', $account['id'])
      ->where('linkedin_message_id', $data['linkedin_message_id'])
      ->first()
      ?->toArray()
    ;

    if ($existing) {
      // the only things that can change later are the read state and the counterpart
      $update = [];
      if (empty($existing['is_read']) && !empty($data['is_read'])) $update['is_read'] = 1;
      if (empty($existing['id_profile']) && !empty($data['id_profile'])) $update['id_profile'] = $data['id_profile'];
      if (count($update) > 0) {
        $mMessage->record->where('id', $existing['id'])->update($update);
        return 'updated';
      }
      return 'exists';
    }

    $mMessage->record->recordCreate(array_merge($data, [
      'id_account' => $account['id'],
      'id_owner' => $account['id_owner'],
      'date_created' => date('Y-m-d H:i:s'),
    ]));

    return 'created';
  }

  /**
   * @return array{id:int,created:bool}
   */
  protected function findOrCreateProfile(array $account, string $urn, array $data): array
  {
    /** @var Profile $mProfile */
    $mProfile = $this->getModel(Profile::class);

    $existing = $mProfile->record
      ->where('linkedin_urn', $urn)
      ->where('id_owner', $account['id_owner'])
      ->first()
      ?->toArray()
    ;

    if ($existing) {
      // fill in details that were unknown so far
      $update = [];
      foreach (['first_name', 'last_name', 'headline', 'profile_url', 'picture_url'] as $key) {
        if (empty($existing[$key]) && !empty($data[$key])) $update[$key] = $data[$key];
      }
      if (count($update) > 0) $mProfile->record->where('id', $existing['id'])->update($update);
      return ['id' => (int) $existing['id'], 'created' => false];
    }

    $created = $mProfile->record->recordCreate([
      'id_owner' => $account['id_owner'],
      'linkedin_urn' => $urn,
      'first_name' => $data['first_name'] ?? ($this->idFromUrn($urn) ?: $urn),
      'last_name' => $data['last_name'] ?? '',
      'headline' => $data['headline'] ?? '',
      'profile_url' => $data['profile_url'] ?? '',
      'picture_url' => $data['picture_url'] ?? '',
      'date_created' => date('Y-m-d H:i:s'),
    ]);

    return ['id' => (int) $created['id'], 'created' => true];
  }

  /**
   * Decorated person objects come in several shapes (`localizedFirstName`, `firstName.localized.*`, ...).
   */
  protected function profileDataFromDecorated(array $d): array
  {
    $pick = function (array $keys) use ($d): string {
      foreach ($keys as $key) {
        $value = $d[$key] ?? null;
        if (is_string($value) && $value !== '') return $value;
        if (is_array($value) && isset($value['localized']) && is_array($value['localized'])) {
          $first = reset($value['localized']);
          if (is_string($first) && $first !== '') return $first;
        }
      }
      return '';
    };

    $data = [
      'first_name' => $pick(['localizedFirstName', 'firstName', 'first_name']),
      'last_name' => $pick(['localizedLastName', 'lastName', 'last_name']),
      'headline' => $pick(['localizedHeadline', 'headline']),
    ];
    $vanity = $pick(['vanityName']);
    if ($vanity !== '') $data['profile_url'] = 'https://www.linkedin.com/in/' . $vanity;

    return array_filter($data, fn($v) => $v !== '');
  }

  protected function isOwnUrn(array $account, string $urn, string $ownerUrn = ''): bool
  {
    if ($urn === '') return false;
    if ($ownerUrn !== '' && $urn === $ownerUrn) return true;
    $memberId = (string) ($account['linkedin_member_id'] ?? '');
    return $memberId !== '' && $this->idFromUrn($urn) === $memberId;
  }

  protected function idFromUrn(string $urn): string
  {
    $pos = strrpos($urn, ':');
    return $pos === false ? $urn : substr($urn, $pos + 1);
  }
}
