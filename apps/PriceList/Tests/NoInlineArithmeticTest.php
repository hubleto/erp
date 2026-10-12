<?php declare(strict_types=1);

namespace Hubleto\App\Community\PriceList\Tests;

/**
 * All price arithmetic lives in Services/. This test fails when typical inline price arithmetic appears elsewhere.
 */
final class NoInlineArithmeticTest extends \Hubleto\Erp\TestCase
{
  public function testNoInlineArithmeticOutsideServices(): void
  {
    $root = realpath(__DIR__ . '/..');
    $patterns = [
      '/\*\s*\(\s*1\s*[-+]/',
      '/\/\s*100\b/',
      '/(?<![>:\w])round\s*\(/',
    ];

    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
      $path = $file->getPathname();
      if (substr($path, -4) !== '.php') continue;
      if (str_contains($path, '/Services/') || str_contains($path, '/Tests/')) continue;

      $content = file_get_contents($path);
      foreach ($patterns as $pattern) {
        $this->assertSame(0, preg_match($pattern, $content), 'Inline arithmetic in ' . $path . ' (pattern ' . $pattern . ')');
      }
    }
  }
}
