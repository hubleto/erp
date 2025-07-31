<?php declare(strict_types=1);

namespace HubletoMain;

use HubletoMain\Controllers\ControllerForgotPassword;
use HubletoMain\Controllers\ControllerResetPassword;
use HubletoMain\Controllers\ControllerSignIn;
use HubletoMain\Controllers\ControllerNotFound;

class Router extends \Hubleto\Framework\Router
{

  public function __construct(\HubletoMain\Loader $main)
  {
    parent::__construct($main);

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

  public function createSignInController(): \HubletoMain\Controller
  {
    return $this->main->di->create(ControllerSignIn::class);
  }

  public function createNotFoundController(): \HubletoMain\Controller
  {
    return $this->main->di->create(ControllerNotFound::class);
  }

  public function createResetPasswordController(): \HubletoMain\Controller
  {
    return $this->main->di->create(ControllerResetPassword::class);
  }

  public function httpGet(array $routes): void
  {
    parent::httpGet($routes);
  }

}
