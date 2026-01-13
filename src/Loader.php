<?php declare(strict_types=1);

namespace Hubleto\Erp;


use Hubleto\App\Community\Auth\AuthProvider;
use Hubleto\App\Community\Auth\Controllers\SignIn;
use Hubleto\App\Community\Settings\PermissionsManager;
use Hubleto\Framework\DependencyInjection;

class Loader extends \Hubleto\Framework\Loader
{

  public string $translationContext = 'Hubleto\\Erp\\Loader';

  /**
   * Class construtor.
   *
   * @param array $config
   * 
   */
  public function __construct(array $config = [])
  {
    parent::__construct($config);

    DependencyInjection::setServiceProviders([
      \Hubleto\Framework\Renderer::class => Renderer::class,
      \Hubleto\Framework\Env::class => Env::class,
      \Hubleto\Framework\Locale::class => Locale::class,

      \Hubleto\Framework\Controllers\NotFound::class => Controllers\NotFound::class,
      \Hubleto\Framework\Controllers\Desktop::class => \Hubleto\App\Community\Desktop\Controllers\Desktop::class,
    ]);

    // Todo: this should be part of the app itself
    DependencyInjection::setServiceProviders([
      \Hubleto\Framework\PermissionsManager::class => PermissionsManager::class,
      \Hubleto\Framework\AuthProvider::class => AuthProvider::class,
      \Hubleto\Framework\Controllers\SignIn::class => SignIn::class,
      \Hubleto\Framework\Models\User::class => \Hubleto\App\Community\Auth\Models\User::class,
      \Hubleto\Framework\Models\Token::class => \Hubleto\App\Community\Auth\Models\Token::class,
    ]);

    // run hook
    $this->hookManager()->run('core:bootstrap-end', [$this]);

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

      date_default_timezone_set($this->locale()->getTimezone());

      // set user language
      $setLanguage = $this->router()->urlParamAsString('set-language');
      $authProvider = $this->authProvider();

      if (
        !empty($setLanguage)
        && !empty(\Hubleto\App\Community\Auth\Models\User::ENUM_LANGUAGES[$setLanguage])
      ) {
        $mUser = $this->getModel(\Hubleto\App\Community\Auth\Models\User::class);
        $mUser->record
          ->where('id', $authProvider->getUserId())
          ->update(['language' => $setLanguage])
        ;
        $authProvider->setUserLanguage($setLanguage);

        $date = date("D, d M Y H:i:s", strtotime('+1 year')) . 'GMT';
        header("Set-Cookie: language={$setLanguage}; EXPIRES{$date};");
        setcookie('incorrectLogin', '1');
        $this->router()->redirectTo('');
      }

      if ($authProvider->isUserInSession()) {
        $authProvider->updateUserInSession($authProvider->getUserFromDatabase());
      }

      if (strlen($authProvider->getUserLanguage()) !== 2) {
        $authProvider->setUserLanguage('en');
      }

      // add core routes
      $this->router()->get([
        '/^api\/get-apps-info\/?$/' => Api\GetAppsInfo::class,
        '/^api\/get-users\/?$/' => Api\GetUsers::class,
        '/^api\/log-javascript-error\/?$/' => Api\LogJavascriptError::class,
        '/^api\/dictionary\/?$/' => Api\Dictionary::class,
        '/^api\/get-chart-data\/?$/' => Api\GetTemplateChartData::class,
        '/^api\/get-table-columns-customize\/?$/' => Api\GetTableColumnsCustomize::class,
        '/^api\/save-table-columns-customize\/?$/' => Api\SaveTableColumnsCustomize::class,
        '/^api\/reset-table-columns-customize\/?$/' => Api\ResetTableColumnsCustomize::class,
        '/^api\/table-export-csv\/?$/' => Api\TableExportCsv::class,
        '/^api\/table-import-csv\/?$/' => Api\TableImportCsv::class,
        '/^api\/search\/?$/' => Api\Search::class,
      ]);

      // run hook
      $this->hookManager()->run('core:init-end', [$this]);
    } catch (\Exception $e) {
      echo "HUBLETO INIT failed: [".get_class($e)."] ".$e->getMessage() . "\n";
      echo $e->getTraceAsString() . "\n";
      exit;
    }
  }

}
