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
      '/^issues\/?$/' => Controllers\Issues::class,
      '/^issues\/mail-accounts\/?$/' => Controllers\MailAccounts::class,
    ]);

    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => 'Issues Mail Accounts', // or $this->translate('EventFeedback')
      'icon' => 'fas fa-table',
      'url' => 'issues/mail-accounts',
    ]);

    $appMenu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    $appMenu->addItem($this, 'issues', $this->translate('Issues'), 'fas fa-table');
    $appMenu->addItem($this, 'issues/mail-accounts', $this->translate('Mail accounts'), 'fas fa-list');
  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Issue::class)->dropTableIfExists()->install();
      $this->getModel(Models\MailAccount::class)->dropTableIfExists()->install();
   }
  }

  // generateDemoData
  public function generateDemoData(): void
  {
    // Create any demo data to promote your app.
  }

}
