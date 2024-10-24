<?php

namespace CeremonyCrmApp\Modules\Core\Settings;

use CeremonyCrmApp\Modules\Core\Settings\Models\Permission;

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
        '/user-roles' => 'UserRoles',
        '/profiles' => 'Profiles',
        '/settings' => 'Settings',
        '/tags' => 'Tags',
        '/activity-types' => 'ActivityTypes',
        '/countries' => 'Countries',
        '/currencies' => 'Currencies',
        '/labels' => 'Labels',
        '/lead-statuses' => 'LeadStatuses',
        '/deal-statuses' => 'DealStatuses',
        '/pipelines' => 'Pipelines',
        '/permissions' => 'Permissions',
      ]
    );
  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 99100, 'settings', $this->app->translate('Settings'), 'fas fa-cog');

    if (str_starts_with($this->app->requestedUri, 'settings')) {
      $sidebar->addHeading1(2, 99200, $this->app->translate('Settings'));
      $sidebar->addLink(2, 99201, 'settings/users', $this->app->translate('Users'), 'fas fa-user');
      $sidebar->addLink(2, 99202, 'settings/user-roles', $this->app->translate('User Roles'), 'fas fa-user-group');
      $sidebar->addLink(2, 99203, 'settings/profiles', $this->app->translate('Profiles'), 'fas fa-id-card');
      $sidebar->addLink(2, 99204, 'settings/settings', $this->app->translate('Settings'), 'fas fa-cog');
      $sidebar->addLink(2, 99205, 'settings/permissions', $this->app->translate('Permissions'), 'fas fa-shield-halved');
      $sidebar->addLink(2, 99206, 'settings/tags', $this->app->translate('Tags'), 'fas fa-tags');
      $sidebar->addLink(2, 99207, 'settings/activity-types', $this->app->translate('Activity Types'), 'fas fa-layer-group');
      $sidebar->addLink(2, 99208, 'settings/countries', $this->app->translate('Countries'), 'fas fa-globe');
      $sidebar->addLink(2, 99209, 'settings/currencies', $this->app->translate('Currencies'), 'fas fa-dollar-sign');
      $sidebar->addLink(2, 99210, 'settings/labels', $this->app->translate('Labels'), 'fas fa-tags');
      $sidebar->addLink(2, 99211, 'settings/lead-statuses', $this->app->translate('Lead Statuses'), 'fas fa-arrow-down-short-wide');
      $sidebar->addLink(2, 99212, 'settings/pipelines', $this->app->translate('Pipelines'), 'fas fa-bars-progress');
    }
  }

  public function generateTestData()
  {
    $mSetting = new Models\Setting($this->app);
    $mSetting->install();

    $mCountry = new Models\Country($this->app);
    $mCountry->install();
  }

  public function createPermissions()
  {
    $mPermission = new Permission($this->app);
    $permissions = [
      "Modules/Core/Settings/Models/ActivityType:Create",
      "Modules/Core/Settings/Models/ActivityType:Read",
      "Modules/Core/Settings/Models/ActivityType:Update",
      "Modules/Core/Settings/Models/ActivityType:Delete",
      "Modules/Core/Settings/Models/Country:Create",
      "Modules/Core/Settings/Models/Country:Read",
      "Modules/Core/Settings/Models/Country:Update",
      "Modules/Core/Settings/Models/Country:Delete",
      "Modules/Core/Settings/Models/Currency:Create",
      "Modules/Core/Settings/Models/Currency:Read",
      "Modules/Core/Settings/Models/Currency:Update",
      "Modules/Core/Settings/Models/Currency:Delete",
      "Modules/Core/Settings/Models/Label:Create",
      "Modules/Core/Settings/Models/Label:Read",
      "Modules/Core/Settings/Models/Label:Update",
      "Modules/Core/Settings/Models/Label:Delete",
      "Modules/Core/Settings/Models/Pipeline:Create",
      "Modules/Core/Settings/Models/Pipeline:Read",
      "Modules/Core/Settings/Models/Pipeline:Update",
      "Modules/Core/Settings/Models/Pipeline:Delete",
      "Modules/Core/Settings/Models/PipelineStep:Create",
      "Modules/Core/Settings/Models/PipelineStep:Read",
      "Modules/Core/Settings/Models/PipelineStep:Update",
      "Modules/Core/Settings/Models/PipelineStep:Delete",
      "Modules/Core/Settings/Models/Profile:Create",
      "Modules/Core/Settings/Models/Profile:Read",
      "Modules/Core/Settings/Models/Profile:Update",
      "Modules/Core/Settings/Models/Profile:Delete",
      "Modules/Core/Settings/Models/Setting:Create",
      "Modules/Core/Settings/Models/Setting:Read",
      "Modules/Core/Settings/Models/Setting:Update",
      "Modules/Core/Settings/Models/Setting:Delete",
      "Modules/Core/Settings/Models/Tag:Create",
      "Modules/Core/Settings/Models/Tag:Read",
      "Modules/Core/Settings/Models/Tag:Update",
      "Modules/Core/Settings/Models/Tag:Delete",
      "Modules/Core/Settings/Models/User:Create",
      "Modules/Core/Settings/Models/User:Read",
      "Modules/Core/Settings/Models/User:Update",
      "Modules/Core/Settings/Models/User:Delete",
      "Modules/Core/Settings/Models/UserRole:Create",
      "Modules/Core/Settings/Models/UserRole:Read",
      "Modules/Core/Settings/Models/UserRole:Update",
      "Modules/Core/Settings/Models/UserRole:Delete",
      "Modules/Core/Settings/Models/UserHasRole:Create",
      "Modules/Core/Settings/Models/UserHasRole:Read",
      "Modules/Core/Settings/Models/UserHasRole:Update",
      "Modules/Core/Settings/Models/UserHasRole:Delete",
      "Modules/Core/Settings/Models/Permission:Create",
      "Modules/Core/Settings/Models/Permission:Read",
      "Modules/Core/Settings/Models/Permission:Update",
      "Modules/Core/Settings/Models/Permission:Delete",
      "Modules/Core/Setting/Controllers/ActivityType",
      "Modules/Core/Setting/Controllers/Country",
      "Modules/Core/Setting/Controllers/Currency",
      "Modules/Core/Setting/Controllers/Label",
      "Modules/Core/Setting/Controllers/Pipeline",
      "Modules/Core/Setting/Controllers/PipelineStep",
      "Modules/Core/Setting/Controllers/Profile",
      "Modules/Core/Setting/Controllers/Setting",
      "Modules/Core/Setting/Controllers/Tag",
      "Modules/Core/Setting/Controllers/User",
      "Modules/Core/Setting/Controllers/UserRole",
      "Modules/Core/Setting/Controllers/UserHasRole",
      "Modules/Core/Setting/Controllers/Permissions",
    ];

    foreach ($permissions as $key => $permission_string) {
      $mPermission->eloquent->create([
        "permission_string" => $permission_string
      ]);
    }
  }
}

