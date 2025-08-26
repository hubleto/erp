<?php declare(strict_types=1);

namespace HubletoMain;

use HubletoMain\Controllers\ControllerForgotPassword;
use HubletoMain\Controllers\ControllerResetPassword;

use HubletoMain\Controllers\ControllerNotFound;

class Router extends \Hubleto\Framework\Router
{

  public function __construct(\HubletoMain\Loader $main)
  {
    parent::__construct($main);

  }

}
