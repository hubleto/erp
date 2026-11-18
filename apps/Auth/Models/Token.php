<?php

namespace Hubleto\App\Community\Auth\Models;

use Hubleto\App\Community\Auth\TwoFactorHelper;
use Hubleto\Erp\Model;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Varchar;

/**
 * Model for storing various validation tokens. Stored in 'tokens' SQL table.
 *
 * @package DefaultModels
 */
class Token extends \Hubleto\Framework\Models\Token {

  public const int TOKEN_TYPE_USER_FORGOT_PASSWORD = 551155;
  public const int TOKEN_TYPE_USER_REMEMBER_ME = 661166;
  public const int TOKEN_TYPE_USER_SIGN_IN_CODE = 771177;

  // results of verifySignInCode()
  public const string CODE_OK = 'ok';
  public const string CODE_INVALID = 'invalid';
  public const string CODE_EXPIRED = 'expired';
  public const string CODE_LOCKED = 'locked';

  public string $table = "tokens";
  public ?string $lookupSqlValue = "{%TABLE%}.token";
  public $tokenTypes = [];
  public string $recordManagerClass = RecordManagers\Token::class;

  public bool $disableAuditLog = true;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      "type" => new Varchar($this, $this->translate('Token type')),
      "valid_to" => new DateTime($this, $this->translate('Expiration date')),
      "token" => new Varchar($this, $this->translate('Token')),
      "attempts" => new Integer($this, $this->translate('Failed attempts')),
    ]);
  }

  public function indexes(array $indexes = []): array
  {
    return parent::indexes([
      "uid" => [
        "type" => "index",
        "columns" => [
          "token" => [
            "order" => "asc",
          ],
        ],
      ],
    ]);
  }

  public function isTokenTypeRegistered($type) {
    return in_array($type, $this->tokenTypes);
  }

  public function registerTokenType($type) {
    if (!in_array($type, $this->tokenTypes)) {
      $this->tokenTypes[] = $type;
    } else {
      throw new \Hubleto\Framework\Exceptions\GeneralException("Duplicate token type: {$type}");
    }
  }

  public function generateToken($tokenSalt, $tokenType, $validTo = NULL) {
    $token = uniqid()."-".md5($tokenSalt);

    if (!in_array($tokenType, $this->tokenTypes)) {
      throw new \Hubleto\Framework\Exceptions\GeneralException("Unknown token type: {$tokenType}");
    }

    if ($validTo === NULL) {
      $validTo = date("Y-m-d H:i:s", strtotime("+ 3 day", time()));
    }

    if (strtotime($validTo) < time()) {
      throw new \Hubleto\Framework\Exceptions\GeneralException("Token validity can not be in the past.");
    }

    $tokenId = $this->record->recordCreate([
      "type" => $tokenType,
      "valid_to" => $validTo,
      "token" => $token,
    ]);

    return $tokenId;
  }

  public function validateToken($token, int $token_type = 0): int|bool {
    $tokenQuery = $this->record
      ->where("token", $token)
      ->where("type", $token_type)
      ->where("valid_to", ">", date("Y-m-d H:i:s"))
    ;

    $tokenData = $tokenQuery->get()->toArray();

    if (!is_array($tokenData)) {
      throw new \Hubleto\Framework\Exceptions\InvalidToken($token);
    }

    return $tokenData[0]['id'] ?? false;
  }

  /**
   * Returns all non-expired sign-in code tokens of the user (newest first).
   *
   * The tokens are found through the `user_has_tokens` junction table. A 6-digit code is NOT unique
   * across users, so a code must never be validated without restricting it to a particular user.
   *
   * @param int $idUser
   *
   * @return array<int, array<string, mixed>>
   *
   */
  public function getActiveSignInCodes(int $idUser): array
  {
    /** @var UserHasToken $mUserHasToken */
    $mUserHasToken = $this->getModel(UserHasToken::class);

    $tokenIds = $mUserHasToken->record->where('id_user', $idUser)->pluck('id_token')->all();
    if (empty($tokenIds)) {
      return [];
    }

    return $this->record
      ->whereIn('id', $tokenIds)
      ->where('type', self::TOKEN_TYPE_USER_SIGN_IN_CODE)
      ->where('valid_to', '>', date('Y-m-d H:i:s'))
      ->orderBy('id', 'desc')
      ->get()
      ->toArray()
    ;
  }

  /**
   * Issues a new sign-in code for the user: stores it as a token of type TOKEN_TYPE_USER_SIGN_IN_CODE
   * and links it to the user. Previous codes of the user are removed.
   *
   * The number of failed attempts of a previous, still valid code is carried over to the new one,
   * so requesting a new code does not reset the brute-force protection. When the limit has been
   * reached, no new code is issued until the locked code expires (returns false).
   *
   * @param int $idUser
   * @param int $validMinutes
   * @param int $maxAttempts
   *
   * @return array{id: int, token: string, valid_to: string}|false
   *
   */
  public function issueSignInCode(int $idUser, int $validMinutes, int $maxAttempts): array|false
  {
    $this->deleteExpiredSignInCodes();

    $active = $this->getActiveSignInCodes($idUser);

    $carriedAttempts = 0;
    foreach ($active as $activeToken) {
      $carriedAttempts = max($carriedAttempts, (int) ($activeToken['attempts'] ?? 0));
    }

    if ($carriedAttempts >= $maxAttempts) {
      return false;
    }

    $this->deleteTokensWithLinks(array_map('intval', array_column($active, 'id')));

    $code = TwoFactorHelper::randomNumericCode();
    $validTo = date('Y-m-d H:i:s', strtotime('+ ' . $validMinutes . ' minute', time()));

    $token = $this->record->recordCreate([
      'type' => self::TOKEN_TYPE_USER_SIGN_IN_CODE,
      'valid_to' => $validTo,
      'token' => $code,
      'attempts' => $carriedAttempts,
    ]);

    /** @var UserHasToken $mUserHasToken */
    $mUserHasToken = $this->getModel(UserHasToken::class);
    $mUserHasToken->record->recordCreate([
      'id_user' => $idUser,
      'id_token' => $token['id'],
    ]);

    return ['id' => (int) $token['id'], 'token' => $code, 'valid_to' => $validTo];
  }

  /**
   * Checks the code against the user's active sign-in code. Every check counts as an attempt.
   * A valid code is consumed (removed) so it cannot be used twice.
   *
   * @param int $idUser
   * @param string $code Normalized code (see TwoFactorHelper::normalizeCode()).
   * @param int $maxAttempts
   *
   * @return string One of self::CODE_OK, CODE_INVALID, CODE_EXPIRED, CODE_LOCKED.
   *
   */
  public function verifySignInCode(int $idUser, string $code, int $maxAttempts): string
  {
    $active = $this->getActiveSignInCodes($idUser);
    if (empty($active)) {
      return self::CODE_EXPIRED;
    }

    $token = $active[0];
    $idToken = (int) $token['id'];

    if ((int) ($token['attempts'] ?? 0) >= $maxAttempts) {
      return self::CODE_LOCKED;
    }

    // Count the attempt first (atomic increment) so parallel requests cannot exceed the limit.
    $this->record->where('id', $idToken)->increment('attempts');
    $attempts = (int) $this->record->where('id', $idToken)->value('attempts');

    if ($attempts > $maxAttempts) {
      return self::CODE_LOCKED;
    }

    if ($code !== '' && hash_equals((string) $token['token'], $code)) {
      $this->deleteTokensWithLinks(array_map('intval', array_column($active, 'id')));
      return self::CODE_OK;
    }

    return $attempts >= $maxAttempts ? self::CODE_LOCKED : self::CODE_INVALID;
  }

  /**
   * Deletes tokens together with their rows in the `user_has_tokens` junction table
   * (the junction rows must go first because of the foreign key).
   *
   * @param array<int, int> $tokenIds
   *
   * @return void
   *
   */
  public function deleteTokensWithLinks(array $tokenIds): void
  {
    if (empty($tokenIds)) {
      return;
    }

    /** @var UserHasToken $mUserHasToken */
    $mUserHasToken = $this->getModel(UserHasToken::class);
    $mUserHasToken->record->whereIn('id_token', $tokenIds)->delete();
    $this->record->whereIn('id', $tokenIds)->delete();
  }

  /**
   * Housekeeping: removes expired sign-in codes of all users.
   *
   * @return void
   *
   */
  public function deleteExpiredSignInCodes(): void
  {
    $expiredIds = $this->record
      ->where('type', self::TOKEN_TYPE_USER_SIGN_IN_CODE)
      ->where('valid_to', '<=', date('Y-m-d H:i:s'))
      ->pluck('id')
      ->all()
    ;

    $this->deleteTokensWithLinks(array_map('intval', $expiredIds));
  }

  public function deleteToken($tokenId) {
    $this->record->where('id', $tokenId)->delete();
  }
}
