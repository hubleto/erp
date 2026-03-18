<?php

namespace Hubleto\App\Community\AiAssistent;

class Loader extends \Hubleto\Erp\App
{
  public bool $permittedForAllUsers = true;

  // init
  public function init(): void
  {
    parent::init();

    // Add app routes.
    $this->router()->get([
      '/^ai-assistant\/?$/' => Controllers\Home::class,
      '/^settings\/ai-assistant\/?$/' => Controllers\Settings::class,
    ]);
    
    $this->router()->get([
      '/^api\/ai-assistant\/chat\/?$/' => Controllers\Chat::class,
    ]);

    // DO NOT DELETE FOLLOWING LINE, OR `php hubleto` WILL NOT GENERATE CODE HERE
    //@hubleto-cli:routes

    // Add placeholder for custom settings.
    // This will be displayed in the Settings app, under the "All settings" card.
    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('AIAssistant'),
      'icon' => 'fas fa-robot',
      'url' => 'settings/ai-assistant',
    ]);

    // Add app menu item
    $appMenu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    $appMenu->addItem($this, 'ai-assistant', $this->translate('AIAssistant'), 'fas fa-robot');
  }

  // upgradeSchema
  public function installApp(int $round): void
  {
    if ($round == 1) {
      // DO NOT DELETE FOLLOWING LINE, OR `php hubleto` WILL NOT GENERATE CODE HERE
      //@hubleto-cli:upgrade-schema
    }
    if ($round == 2) {
      // do something in the 2nd round, if required
    }
    if ($round == 3) {
      // do something in the 3rd round, if required
    }
  }

  // generateDemoData
  public function generateDemoData(): void
  {
    // Create any demo data to promote your app.
  }

}
