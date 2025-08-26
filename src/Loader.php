<?php declare(strict_types=1);

namespace HubletoMain;

use Hubleto\Framework\DependencyInjection;

/**
 * Main Hubleto class. This class is always referenced
 * as `$this->main` or `$main`.
 */
class Loader extends \Hubleto\Framework\Loader
{

  public \HubletoMain\Emails\EmailProvider $email;
  public \HubletoMain\Emails\EmailWrapper $emails;

  /**
   * If set to true, this run is managed as premium.
   *
   * @var bool
   */
  public bool $isPremium = false;

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
      \Hubleto\Framework\Auth\DefaultProvider::class => AuthProvider::class,
      \Hubleto\Framework\Router::class => Router::class,
      \Hubleto\Framework\Renderer::class => Renderer::class,
      \Hubleto\Framework\Controllers\DesktopController::class => \HubletoApp\Community\Desktop\Controllers\Desktop::class,
    ]);

    // Emails
    $this->email = DependencyInjection::create($this, \HubletoMain\Emails\EmailProvider::class);

    // DEPRECATED
    $this->emails = DependencyInjection::create($this, \HubletoMain\Emails\EmailWrapper::class);
    $this->emails->emailProvider = $this->email;

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

      $this->email->init();

      $this->getHookManager()->run('core:init-end', [$this]);
    } catch (\Exception $e) {
      echo "HUBLETO INIT failed: [".get_class($e)."] ".$e->getMessage() . "\n";
      echo $e->getTraceAsString() . "\n";
      exit;
    }
  }

}
