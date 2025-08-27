<?php declare(strict_types=1);

namespace Hubleto\Erp;

class Hook extends \Hubleto\Framework\Core
{

  // public function __construct(public \Hubleto\Framework\Loader $main)
  // {
  // }

  /**
   * [Description for run]
   *
   * @param string $event
   * @param array $args
   * 
   * @return void
   * 
   */
  public function run(string $event, array $args): void
  {
    // to be overriden
  }

}
