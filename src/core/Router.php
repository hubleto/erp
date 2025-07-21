<?php

namespace HubletoMain\Core;

use HubletoMain\Core\Controllers\ControllerForgotPassword;
use HubletoMain\Core\Controllers\ControllerResetPassword;
use HubletoMain\Core\Controllers\ControllerSignIn;
use HubletoMain\Core\Controllers\ControllerNotFound;

class Router extends \ADIOS\Core\Router
{

  public \HubletoMain $main;

  public function __construct(\HubletoMain $app)
  {
    parent::__construct($app);

    $this->main = $app;

    $this->httpGet([
      '/^api\/get-apps-info\/?$/' => Api\GetAppsInfo::class,
      '/^api\/log-javascript-error\/?$/' => Api\LogJavascriptError::class,
      '/^api\/dictionary\/?$/' => Api\Dictionary::class,
      '/^api\/get-chart-data\/?$/' =>  Api\GetTemplateChartData::class,
      '/^api\/get-table-columns-customize\/?$/' =>  Api\GetTableColumnsCustomize::class,
      '/^api\/save-table-columns-customize\/?$/' =>  Api\SaveTableColumnsCustomize::class,
      '/^api\/table-export-csv\/?$/' =>  Api\TableExportCsv::class,
      '/^api\/table-import-csv\/?$/' =>  Api\TableImportCsv::class,
      '/^reset-password$/' => ControllerResetPassword::class,
      '/^forgot-password$/' => ControllerForgotPassword::class,
    ]);
  }

  public function createSignInController(): \ADIOS\Core\Controller
  {
    return new ControllerSignIn($this->main);
  }

  public function createNotFoundController(): \ADIOS\Core\Controller
  {
    return new ControllerNotFound($this->main);
  }

  public function createResetPasswordController(): \ADIOS\Core\Controller
  {
    return new ControllerResetPassword($this->main);
  }

  public function createDesktopController(): \ADIOS\Core\Controller
  {
    return new \HubletoApp\Community\Desktop\Controllers\Desktop($this->main);
  }

  public function httpGet(array $routes): void
  {
    parent::httpGet($routes);
  }

}
