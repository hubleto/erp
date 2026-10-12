<?php declare(strict_types=1);

namespace Hubleto\App\Community\PriceList\Tests;

use Hubleto\App\Community\PriceList\Services\PriceCalculator;
use Hubleto\Erp\Interfaces\PriceCalculatorInterface;

/**
 * Fails when the community interface changes (e.g., the order of parameters) and PriceList does not follow.
 */
final class PriceCalculatorInterfaceTest extends \Hubleto\Erp\TestCase
{
  public function testImplementsInterface(): void
  {
    $this->assertTrue(is_subclass_of(PriceCalculator::class, PriceCalculatorInterface::class));
  }

  public function testSignaturesEqualInterface(): void
  {
    $interface = new \ReflectionClass(PriceCalculatorInterface::class);
    foreach ($interface->getMethods() as $method) {
      $mine = new \ReflectionMethod(PriceCalculator::class, $method->getName());
      $this->assertSame($method->getNumberOfParameters(), $mine->getNumberOfParameters(), $method->getName());
      foreach ($method->getParameters() as $index => $parameter) {
        $myParameter = $mine->getParameters()[$index];
        $this->assertSame($parameter->getName(), $myParameter->getName(), $method->getName());
        $this->assertSame((string) $parameter->getType(), (string) $myParameter->getType(), $method->getName());
      }
    }
  }
}
