<?php

namespace HubletoMain\Core;

use HubletoMain\Core\Controllers\ControllerForgotPassword;
use HubletoMain\Core\Controllers\ControllerResetPassword;
use HubletoMain\Core\Controllers\ControllerSignIn;

class Router extends \ADIOS\Core\Router {

  public function __construct(\ADIOS\Core\Loader $app)
  {
    parent::__construct($app);

    $this->httpGet([
      '/^api\/log-javascript-error\/?$/' => LogJavascriptError::class,
      '/^api\/dictionary\/?$/' => Dictionary::class,
      '/^api\/get-chart-data\/?$/' =>  \HubletoMain\Core\Api\GetTemplateChartData::class,
      '/^reset-password$/' => ControllerResetPassword::class,
      '/^forgot-password$/' => ControllerForgotPassword::class,
    ]);
  }

  public function createSignInController(): \ADIOS\Core\Controller
  {
    return new ControllerSignIn($this->app);
  }

  public function createResetPasswordController(): \ADIOS\Core\Controller
  {
    return new ControllerResetPassword($this->app);
  }

  public function createDesktopController(): \ADIOS\Core\Controller
  {
    // return new \HubletoMain\Core\ControllerDesktop($this->app);
    return new \HubletoApp\Community\Desktop\Controllers\Desktop($this->app);
  }

}
