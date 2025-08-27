<?php declare(strict_types=1);

namespace HubletoApp\Help\Customers\Tests;

use PHPUnit\Framework\TestCase;

final class RenderAllRoutesTest extends TestCase
{

  public function testRenderAllRoutes(): void
  {
    $main = \HubletoMain\Loader::getGlobalApp();

    $html = $main->render('help');
    $this->assertStringContainsString('app-main-title', $html);
    $this->assertStringNotContainsStringIgnoringCase('error', $html);
  }

}
