<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class GeneralTest extends TestCase
{
  public function testBootstrap(): void
  {
    $main = \HubletoMain\Loader::getGlobalApp();
    $this->assertInstanceOf(\HubletoMain\Loader::class, $main);
  }
}