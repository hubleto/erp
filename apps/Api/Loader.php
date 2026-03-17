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
      '/^api\/keys(\/(?<recordId>\d+))?\/?$/' => Controllers\Keys::class,
      '/^api\/keys\/add?\/?$/' => ['controller' => Controllers\Keys::class, 'vars' => [ 'recordId' => -1 ]],
      '/^api\/permissions(\/(?<recordId>\d+))?\/?$/' => Controllers\Permissions::class,
      '/^api\/permissions\/add?\/?$/' => ['controller' => Controllers\Permissions::class, 'vars' => [ 'recordId' => -1 ]],
      '/^api\/usage(\/(?<recordId>\d+))?\/?$/' => Controllers\Usages::class,
      '/^api\/usage\/add?\/?$/' => ['controller' => Controllers\Usages::class, 'vars' => [ 'recordId' => -1 ]],
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
  public function upgradeSchema(int $round): void
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
      <div class="flex flex-col gap-2">
        <a class="btn btn-square btn-primary-outline" href="' . $this->env()->projectUrl . '/api">
          <span class="icon"><i class="fas fa-arrow-right-arrow-left"></i></span>
          <span class="text">' . $this->translate('API') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/api/keys">
          <span class="icon"><i class="fas fa-key"></i></span>
          <span class="text">' . $this->translate('Keys') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/api/usage">
          <span class="icon"><i class="fas fa-check-double"></i></span>
          <span class="text">' . $this->translate('Usage log') . '</span>
        </a>
      </div>
    ';
  }

}
