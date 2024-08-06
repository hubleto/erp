<?php

namespace CeremonyCrmApp\Modules\Core\Settings;

class Loader extends \CeremonyCrmApp\Core\Module {
  public function __construct(\CeremonyCrmApp $app) {
    parent::__construct($app);

    $this->registerModel(\CeremonyCrmApp\Modules\Core\Settings\Models\User::class);
    $this->registerModel(\CeremonyCrmApp\Modules\Core\Settings\Models\UserRole::class);
    $this->registerModel(\CeremonyCrmApp\Modules\Core\Settings\Models\UserHasRole::class);
    $this->registerModel(\CeremonyCrmApp\Modules\Core\Settings\Models\Setting::class);
  }

  public function addRouting(\CeremonyCrmApp\Core\Router $router) {
    $router->addRoutingGroup(
      'settings',
      'CeremonyCrmApp/Modules/Core/Settings/Controllers',
      'CeremonyCrmApp/Modules/Core/Settings/Views',
      [],
      [
        '' => 'Dashboard',
        '/users' => 'Users',
        '/settings' => 'Settings',
      ]
    );
  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar) {
    $sidebar->addLink(1, 99100, 'settings', $this->app->translate('Settings'), 'fas fa-cog');

    if (str_starts_with($this->app->requestedUri, 'settings')) {
      $sidebar->addHeading1(2, 99200, $this->app->translate('Settings'));
      $sidebar->addLink(2, 99201, 'settings/users', $this->app->translate('Users'), 'fas fa-user');
      $sidebar->addLink(2, 99202, 'settings/settings', $this->app->translate('Settings'), 'fas fa-cog');
    }
  }

  public function generateTestData() {
    $mSetting = new Models\Setting($this->app);
    $mSetting->install();
    $mSetting->eloquent->create(['key' => 'test/setting/example', 'value' => rand(1000, 9999)]);
  }}