<?php declare(strict_types=1);

namespace Hubleto\App\Community\Auth;

/**
 * Small, dependency-free helpers used by the second authentication step
 * (6-digit code sent by email). Kept framework-independent so they can be unit-tested.
 */
final class TwoFactorHelper
{

  /** Number of digits of the sign-in code. */
  public const int CODE_LENGTH = 6;

  /**
   * Generates a zero-padded random numeric code using a cryptographically secure generator.
   *
   * @param int $digits Number of digits (1 - 18).
   *
   * @return string
   *
   */
  public static function randomNumericCode(int $digits = self::CODE_LENGTH): string
  {
    $digits = max(1, min(18, $digits));
    return str_pad((string) random_int(0, (10 ** $digits) - 1), $digits, '0', STR_PAD_LEFT);
  }

  /**
   * Normalizes the code typed by user (removes whitespace and separators, e.g. "123 456" => "123456")
   * and checks that it has the expected format. Returns empty string for a malformed code.
   *
   * @param string $code
   * @param int $digits
   *
   * @return string
   *
   */
  public static function normalizeCode(string $code, int $digits = self::CODE_LENGTH): string
  {
    $code = preg_replace('/[\s\-]+/u', '', $code) ?? '';
    return preg_match('/^[0-9]{' . $digits . '}$/', $code) === 1 ? $code : '';
  }

  /**
   * Masks an email address so it can be displayed safely, e.g. "john.doe@example.com" => "j*******@example.com".
   *
   * @param string $email
   *
   * @return string
   *
   */
  public static function maskEmail(string $email): string
  {
    $email = trim($email);
    $at = strrpos($email, '@');

    if ($at === false || $at < 1) {
      return $email === '' ? '' : self::firstChar($email) . '***';
    }

    $local = substr($email, 0, $at);
    $domain = substr($email, $at);

    // PCRE (/u) is used instead of mb_* functions, so no extension is required
    $length = (int) preg_match_all('/./us', $local);

    return self::firstChar($local) . str_repeat('*', max(2, $length - 1)) . $domain;
  }

  private static function firstChar(string $string): string
  {
    return preg_match('/^./us', $string, $matches) === 1 ? $matches[0] : '';
  }

}
