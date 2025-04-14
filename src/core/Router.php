<?php

namespace HubletoMain\Core;

class Router extends \ADIOS\Core\Router {

  public function __construct(\ADIOS\Core\Loader $app)
  {
    parent::__construct($app);

    $this->httpGet([
      '/^api\/log-javascript-error\/?$/' => LogJavascriptError::class,
      '/^api\/dictionary\/?$/' => Dictionary::class,
      '/^api\/get-chart-data\/?$/' =>  \HubletoMain\Core\Api\GetTemplateChartData::class,
    ]);
  }

  public function createSignInController(): \ADIOS\Core\Controller
  {
    return new \HubletoMain\Core\ControllerSignIn($this->app);
  }

  public function createDesktopController(): \ADIOS\Core\Controller
  {
    // return new \HubletoMain\Core\ControllerDesktop($this->app);
    return new \HubletoApp\Community\Desktop\Controllers\Desktop($this->app);
  }

}
