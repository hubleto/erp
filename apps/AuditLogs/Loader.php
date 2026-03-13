<?php

namespace Hubleto\App\Community\AuditLogs;

class Loader extends \Hubleto\Erp\App
{

  /**
   * Inits the app: adds routes, settings, calendars, event listeners, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^audit-logs\/?$/' => Controllers\AuditLogs::class,
    ]);

    $this->cronManager()->addCron(Crons\DailyDigest::class);

    $this->eventManager()->addEventListener(
      'onModelAfterCreate',
      $this->getService(EventListeners\LogCreatedRecord::class)
    );

    $this->eventManager()->addEventListener(
      'onModelAfterUpdate',
      $this->getService(EventListeners\LogUpdatedRecord::class)
    );

    $this->eventManager()->addEventListener(
      'onModelAfterDelete',
      $this->getService(EventListeners\LogDeletedRecord::class)
    );
  }

  /**
   * [Description for upgradeSchema]
   *
   * @param int $round
   * 
   * @return void
   * 
   */
  public function upgradeSchema(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\AuditLog::class)->upgradeSchema();
    }
  }

}
