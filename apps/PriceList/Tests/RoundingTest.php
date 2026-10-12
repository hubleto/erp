<?php declare(strict_types=1);

namespace Hubleto\App\Community\PriceList\Tests;

use Hubleto\App\Community\PriceList\Services\Rounding;

final class RoundingTest extends \Hubleto\Erp\TestCase
{
  public function testPolicies(): void
  {
    /** @var Rounding $r */
    $r = \Hubleto\Erp\Loader::getGlobalApp()->getService(Rounding::class);

    $this->assertEqualsWithDelta(12.45, $r->round(12.47, ['policy' => Rounding::POLICY_NEAREST, 'step' => 0.05, 'decimals' => 2]), 1e-9);
    $this->assertEqualsWithDelta(12.5, $r->round(12.1, ['policy' => Rounding::POLICY_UP, 'step' => 0.5]), 1e-9);
    $this->assertEqualsWithDelta(12.5, $r->round(12.9, ['policy' => Rounding::POLICY_DOWN, 'step' => 0.5]), 1e-9);
    $this->assertEqualsWithDelta(12.99, $r->round(12.60, ['policy' => Rounding::POLICY_ENDS_IN, 'ending' => 0.99]), 1e-9);
    $this->assertEqualsWithDelta(11.99, $r->round(12.10, ['policy' => Rounding::POLICY_ENDS_IN, 'ending' => 0.99]), 1e-9);
    $this->assertEqualsWithDelta(1.23456789, $r->round(1.23456789, null), 1e-12);
    $this->assertEqualsWithDelta(1.23, $r->round(1.23456, ['decimals' => 2]), 1e-12);
  }
}
