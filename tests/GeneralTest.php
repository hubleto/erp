<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class GeneralTest extends TestCase
{
  public function testBootstrap(): void
  {
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $this->assertInstanceOf(\Hubleto\Erp\Loader::class, $hubleto);
  }
}