<?php

namespace CeremonyCrmApp\Modules\Core\Settings;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);

    $this->registerModel(\CeremonyCrmApp\Modules\Core\Settings\Models\User::class);
    $this->registerModel(\CeremonyCrmApp\Modules\Core\Settings\Models\UserRole::class);
    $this->registerModel(\CeremonyCrmApp\Modules\Core\Settings\Models\UserHasRole::class);
    $this->registerModel(\CeremonyCrmApp\Modules\Core\Settings\Models\Setting::class);
  }

  public function addRouting(\CeremonyCrmApp\Core\Router $router)
  {
    $router->addRoutingGroup(
      'settings',
      'CeremonyCrmApp/Modules/Core/Settings/Controllers',
      'CeremonyCrmApp/Modules/Core/Settings/Views',
      [],
      [
        '' => 'Dashboard',
        '/users' => 'Users',
        '/profiles' => 'Profiles',
        '/settings' => 'Settings',
        '/tags' => 'Tags',
        '/activity-types' => 'ActivityTypes',
        '/countries' => 'Countries',
        '/currencies' => 'Currencies',
        '/labels' => 'Labels',
        '/lead-statuses' => 'LeadStatuses',
        '/deal-statuses' => 'DealStatuses',
      ]
    );
  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 99100, 'settings', $this->app->translate('Settings'), 'fas fa-cog');

    if (str_starts_with($this->app->requestedUri, 'settings')) {
      $sidebar->addHeading1(2, 99200, $this->app->translate('Settings'));
      $sidebar->addLink(2, 99201, 'settings/users', $this->app->translate('Users'), 'fas fa-user');
      $sidebar->addLink(2, 99202, 'settings/profiles', $this->app->translate('Profiles'), 'fas fa-id-card');
      $sidebar->addLink(2, 99203, 'settings/settings', $this->app->translate('Settings'), 'fas fa-cog');
      $sidebar->addLink(2, 99204, 'settings/tags', $this->app->translate('Tags'), 'fas fa-tags');
      $sidebar->addLink(2, 99205, 'settings/activity-types', $this->app->translate('Activity Types'), 'fas fa-layer-group');
      $sidebar->addLink(2, 99206, 'settings/countries', $this->app->translate('Countries'), 'fas fa-globe');
      $sidebar->addLink(2, 99207, 'settings/currencies', $this->app->translate('Currencies'), 'fas fa-dollar-sign');
      $sidebar->addLink(2, 99208, 'settings/labels', $this->app->translate('Labels'), 'fas fa-tags');
      $sidebar->addLink(2, 99209, 'settings/lead-statuses', $this->app->translate('Lead Statuses'), 'fas fa-arrow-down-short-wide');
      $sidebar->addLink(2, 99210, 'settings/deal-statuses', $this->app->translate('Deal Statuses'), 'fas fa-arrow-down-wide-short');
    }
  }

  public function generateTestData()
  {
    $mSetting = new Models\Setting($this->app);
    $mSetting->install();
    $mSetting->eloquent->create(['key' => 'test/setting/example', 'value' => rand(1000, 9999)]);

    $mCountry = new Models\Country($this->app);
    $mCountry->install();
    /* $countries = [
      ['name' => 'United States', 'code' => 'US'],
      ['name' => 'Canada', 'code' => 'CA'],
      ['name' => 'United Kingdom', 'code' => 'UK'],
      ['name' => 'Australia', 'code' => 'AU'],
      ['name' => 'Germany', 'code' => 'DE'],
      ['name' => 'Slovakia', 'code' => 'SK'],
  ];

  foreach ($countries as $country) {
    $mCountry->eloquent->create($country);
  } */
  }}