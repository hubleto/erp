<?php

namespace HubletoMain;

use Hubleto\Framework\Interfaces\AppManagerInterface;

class CoreClass
{
  public Loader $main;

  public function __construct(Loader $main)
  {
    $this->main = $main;
  }

  /**
   * [Description for getAppManager]
   *
   * @return AppManagerInterface
   * 
   */
  public function getAppManager(): AppManagerInterface
  {
    return $this->main->getAppManager();
  }

}