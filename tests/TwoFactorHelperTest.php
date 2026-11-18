<?php declare(strict_types=1);

use Hubleto\App\Community\Auth\TwoFactorHelper;
use PHPUnit\Framework\TestCase;

final class TwoFactorHelperTest extends TestCase
{
  public function testRandomNumericCodeHasFixedLengthAndOnlyDigits(): void
  {
    for ($i = 0; $i < 200; $i++) {
      $code = TwoFactorHelper::randomNumericCode();
      $this->assertSame(6, strlen($code));
      $this->assertMatchesRegularExpression('/^[0-9]{6}$/', $code);
    }
  }

  public function testRandomNumericCodeKeepsLeadingZeros(): void
  {
    $foundLeadingZero = false;
    for ($i = 0; $i < 2000 && !$foundLeadingZero; $i++) {
      $foundLeadingZero = str_starts_with(TwoFactorHelper::randomNumericCode(), '0');
    }
    $this->assertTrue($foundLeadingZero, 'Codes with leading zero must be generated and zero-padded.');
  }

  public function testRandomNumericCodeRespectsRequestedLength(): void
  {
    $this->assertSame(4, strlen(TwoFactorHelper::randomNumericCode(4)));
    $this->assertSame(8, strlen(TwoFactorHelper::randomNumericCode(8)));
  }

  public function testNormalizeCodeAcceptsSeparators(): void
  {
    $this->assertSame('123456', TwoFactorHelper::normalizeCode('123456'));
    $this->assertSame('123456', TwoFactorHelper::normalizeCode(' 123 456 '));
    $this->assertSame('012345', TwoFactorHelper::normalizeCode('012-345'));
  }

  public function testNormalizeCodeRejectsMalformedInput(): void
  {
    $this->assertSame('', TwoFactorHelper::normalizeCode(''));
    $this->assertSame('', TwoFactorHelper::normalizeCode('12345'));
    $this->assertSame('', TwoFactorHelper::normalizeCode('1234567'));
    $this->assertSame('', TwoFactorHelper::normalizeCode('12a456'));
    $this->assertSame('', TwoFactorHelper::normalizeCode("123456\n789"));
  }

  public function testMaskEmail(): void
  {
    $this->assertSame('j*******@example.com', TwoFactorHelper::maskEmail('john.doe@example.com'));
    $this->assertSame('a**@example.com', TwoFactorHelper::maskEmail('a@example.com'));
    $this->assertSame('', TwoFactorHelper::maskEmail(''));
    $this->assertStringNotContainsString('ohn', TwoFactorHelper::maskEmail('john@example.com'));
  }
}
