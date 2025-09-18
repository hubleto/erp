<?php declare(strict_types=1);

namespace Hubleto\App\Help\Customers\Tests;

use PHPUnit\Framework\TestCase;

final class RenderAllRoutesTest extends TestCase
{

  public function testRenderAllRoutes(): void
  {
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    $html = $hubleto->render('help');
    $this->assertStringContainsString('app-main-title', $html);
    $this->assertStringNotContainsStringIgnoringCase('error', $html);
  }

}
