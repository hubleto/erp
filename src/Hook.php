<?php declare(strict_types=1);

namespace HubletoMain;

class Hook extends \Hubleto\Framework\CoreClass
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
