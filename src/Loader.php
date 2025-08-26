<?php declare(strict_types=1);

namespace HubletoMain;

use Hubleto\Framework\DependencyInjection;

class Loader extends \Hubleto\Framework\Loader
{

  /**
   * Class construtor.
   *
   * @param array $config
   * @param int $mode
   * 
   */
  public function __construct(array $config = [])
  {
    parent::__construct($config);

    DependencyInjection::setServiceProviders([
      \Hubleto\Framework\PermissionsManager::class => PermissionsManager::class,
      \Hubleto\Framework\AuthProvider::class => AuthProvider::class,
      \Hubleto\Framework\Renderer::class => Renderer::class,
      
      \Hubleto\Framework\Controllers\SignIn::class => Controllers\SignIn::class,
      \Hubleto\Framework\Controllers\NotFound::class => Controllers\NotFound::class,
      \Hubleto\Framework\Controllers\Desktop::class => \HubletoApp\Community\Desktop\Controllers\Desktop::class,

      \Hubleto\Framework\Models\User::class => \HubletoApp\Community\Settings\Models\User::class,
      
    ]);

    // Finish
    $this->getHookManager()->run('core:bootstrap-end', [$this]);

  }

  /**
   * Init phase after constructing.
   *
   * @return void
   * 
   */
  public function init(): void
  {
    try {
      parent::init();

      $this->getRouter()->httpGet([
        '/^api\/get-apps-info\/?$/' => Api\GetAppsInfo::class,
        '/^api\/log-javascript-error\/?$/' => Api\LogJavascriptError::class,
        '/^api\/dictionary\/?$/' => Api\Dictionary::class,
        '/^api\/get-chart-data\/?$/' =>  Api\GetTemplateChartData::class,
        '/^api\/get-table-columns-customize\/?$/' =>  Api\GetTableColumnsCustomize::class,
        '/^api\/save-table-columns-customize\/?$/' =>  Api\SaveTableColumnsCustomize::class,
        '/^api\/table-export-csv\/?$/' =>  Api\TableExportCsv::class,
        '/^api\/table-import-csv\/?$/' =>  Api\TableImportCsv::class,
        '/^api\/search\/?$/' =>  Api\Search::class,
        '/^reset-password$/' => Controllers\ResetPassword::class,
        '/^forgot-password$/' => Controllers\ForgotPassword::class,
      ]);

      $this->getHookManager()->run('core:init-end', [$this]);
    } catch (\Exception $e) {
      echo "HUBLETO INIT failed: [".get_class($e)."] ".$e->getMessage() . "\n";
      echo $e->getTraceAsString() . "\n";
      exit;
    }
  }

}
