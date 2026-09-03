<?php

namespace Hubleto\App\Community\Api;

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
      '/^api\/?$/' => Controllers\Home::class,
      '/^api\/call?$/' => Controllers\Call::class,
      '/^api\/oauth?$/' => Controllers\OAuth::class,
      '/^api\/keys(\/(?<recordId>\d+))?\/?$/' => Controllers\Keys::class,
      '/^api\/keys\/add?\/?$/' => ['controller' => Controllers\Keys::class, 'vars' => [ 'recordId' => -1 ]],
      '/^api\/permissions(\/(?<recordId>\d+))?\/?$/' => Controllers\Permissions::class,
      '/^api\/permissions\/add?\/?$/' => ['controller' => Controllers\Permissions::class, 'vars' => [ 'recordId' => -1 ]],
      '/^api\/usages(\/(?<recordId>\d+))?\/?$/' => Controllers\Usages::class,
      '/^api\/usages\/add?\/?$/' => ['controller' => Controllers\Usages::class, 'vars' => [ 'recordId' => -1 ]],
    ]);


    /** @var \Hubleto\App\Community\Settings\Loader $settingsApp */
    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('API keys'),
      'icon' => 'fas fa-key',
      'url' => 'api/keys',
    ]);

  }

  // upgradeSchema
  public function installApp(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Key::class)->upgradeSchema();
      $this->getModel(Models\Permission::class)->upgradeSchema();
      $this->getModel(Models\Usage::class)->upgradeSchema();
    }
  }

  /**
   * [Description for renderSecondSidebar]
   *
   * @return string
   *
   */
  public function renderSecondSidebar(): string
  {
    return '
      ' . $this->secondSidebarTitle() . '
      <div class="flex flex-col gap-2">
        ' . $this->secondSidebarButton('api/oauth', 'fas fa-shield-halved', 'OAuth') . '
        ' . $this->secondSidebarButton('api/keys', 'fas fa-key', 'Keys') . '
        ' . $this->secondSidebarButton('api/usage', 'fas fa-check-double', 'Usage log') . '
      </div>
    ';
  }

}
