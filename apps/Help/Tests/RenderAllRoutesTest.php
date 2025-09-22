<?php declare(strict_types=1);

namespace Hubleto\App\Help\Customers\Tests;

use PHPUnit\Framework\TestCase;

final class RenderAllRoutesTest extends \Hubleto\Erp\TestCase
{

  public function testRoutesContainAppMainTitle(): void
  {
    $this->testRouteContainsAppMainTitle('help');
  }

}
