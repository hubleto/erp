<?php

namespace Hubleto\App\Community\Issues;

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
      '/^issues(\/(?<recordId>\d+))?\/?$/' => Controllers\Issues::class,
      '/^issues\/add\/?$/' => ['controller' => Controllers\Issues::class, 'vars' => ['recordId' => -1]],

      '/^issues\/posts(\/(?<recordId>\d+))?\/?$/' => Controllers\Posts::class,
      '/^issues\/posts\/add\/?$/' => ['controller' => Controllers\Posts::class, 'vars' => ['recordId' => -1]],
    ]);

    $this->eventManager()->addEventListener(
      'onMailReceived',
      $this->getService(EventListeners\CreateIssueFromMail::class)
    );

  }

  // upgradeSchema
  public function installApp(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Issue::class)->upgradeSchema();
      $this->getModel(Models\IssueTask::class)->upgradeSchema();
      $this->getModel(Models\Post::class)->upgradeSchema();
   }
  }

  // generateDemoData
  public function generateDemoData(): void
  {
    // Create any demo data to promote your app.
  }

}
