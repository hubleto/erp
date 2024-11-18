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
      '@app/Modules/Core/Settings/Views',
      [],
      [
        '' => 'Dashboard',
        '/users' => 'Users',
        '/user-roles' => 'UserRoles',
        '/profiles' => 'Profiles',
        '/settings' => 'Settings',
        '/tags' => 'Tags',
        '/activity-types' => 'ActivityTypes',
        '/contact-types' => 'ContactTypes',
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
      $sidebar->addLink(2, 99208, 'settings/contact-types', $this->app->translate('Contact Types'), 'fas fa-phone');
      $sidebar->addLink(2, 99209, 'settings/countries', $this->app->translate('Countries'), 'fas fa-globe');
      $sidebar->addLink(2, 99210, 'settings/currencies', $this->app->translate('Currencies'), 'fas fa-dollar-sign');
      $sidebar->addLink(2, 99211, 'settings/labels', $this->app->translate('Labels'), 'fas fa-tags');
      $sidebar->addLink(2, 99212, 'settings/lead-statuses', $this->app->translate('Lead Statuses'), 'fas fa-arrow-down-short-wide');
      $sidebar->addLink(2, 99213, 'settings/pipelines', $this->app->translate('Pipelines'), 'fas fa-bars-progress');
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
      "CeremonyCrmApp/Modules/Core/Settings/Models/ActivityType:Create",
      "CeremonyCrmApp/Modules/Core/Settings/Models/ActivityType:Read",
      "CeremonyCrmApp/Modules/Core/Settings/Models/ActivityType:Update",
      "CeremonyCrmApp/Modules/Core/Settings/Models/ActivityType:Delete",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Country:Create",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Country:Read",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Country:Update",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Country:Delete",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Currency:Create",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Currency:Read",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Currency:Update",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Currency:Delete",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Label:Create",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Label:Read",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Label:Update",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Label:Delete",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Pipeline:Create",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Pipeline:Read",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Pipeline:Update",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Pipeline:Delete",
      "CeremonyCrmApp/Modules/Core/Settings/Models/PipelineStep:Create",
      "CeremonyCrmApp/Modules/Core/Settings/Models/PipelineStep:Read",
      "CeremonyCrmApp/Modules/Core/Settings/Models/PipelineStep:Update",
      "CeremonyCrmApp/Modules/Core/Settings/Models/PipelineStep:Delete",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Profile:Create",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Profile:Read",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Profile:Update",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Profile:Delete",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Setting:Create",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Setting:Read",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Setting:Update",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Setting:Delete",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Tag:Create",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Tag:Read",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Tag:Update",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Tag:Delete",
      "CeremonyCrmApp/Modules/Core/Settings/Models/User:Create",
      "CeremonyCrmApp/Modules/Core/Settings/Models/User:Read",
      "CeremonyCrmApp/Modules/Core/Settings/Models/User:Update",
      "CeremonyCrmApp/Modules/Core/Settings/Models/User:Delete",
      "CeremonyCrmApp/Modules/Core/Settings/Models/UserRole:Create",
      "CeremonyCrmApp/Modules/Core/Settings/Models/UserRole:Read",
      "CeremonyCrmApp/Modules/Core/Settings/Models/UserRole:Update",
      "CeremonyCrmApp/Modules/Core/Settings/Models/UserRole:Delete",
      "CeremonyCrmApp/Modules/Core/Settings/Models/UserHasRole:Create",
      "CeremonyCrmApp/Modules/Core/Settings/Models/UserHasRole:Read",
      "CeremonyCrmApp/Modules/Core/Settings/Models/UserHasRole:Update",
      "CeremonyCrmApp/Modules/Core/Settings/Models/UserHasRole:Delete",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Permission:Create",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Permission:Read",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Permission:Update",
      "CeremonyCrmApp/Modules/Core/Settings/Models/Permission:Delete",
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/ActivityType",
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/Country",
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/Currency",
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/Label",
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/Pipeline",
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/PipelineStep",
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/Profile",
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/Setting",
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/Tag",
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/User",
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/UserRole",
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/UserHasRole",
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/Permissions",
    ];

    foreach ($permissions as $key => $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}

