<?php

namespace HubletoMain;

use Hubleto\Framework\Interfaces\AppManagerInterface;
use Hubleto\Framework\Router;
use Hubleto\Framework\Config;

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

  /**
   * [Description for getRouter]
   *
   * @return Router
   * 
   */
  public function getRouter(): Router
  {
    return $this->main->getRouter();
  }

  /**
   * [Description for getConfig]
   *
   * @return Router
   * 
   */
  public function getConfig(): Config
  {
    return $this->main->getConfig();
  }

}