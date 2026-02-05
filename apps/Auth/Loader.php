<?php

namespace Hubleto\App\Community\Auth;

use Hubleto\Framework\DependencyInjection;

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

    $this->setConfigAsInteger('sidebarOrder', 0);

    $this->router()->get([
//      '/^api\/?$/' => Controllers\Home::class,
      '/^sign-in$/' => Controllers\SignIn::class,
      '/^reset-password$/' => Controllers\ResetPassword::class,
      '/^forgot-password$/' => Controllers\ForgotPassword::class,
    ]);

    DependencyInjection::setServiceProviders([
      \Hubleto\Framework\AuthProvider::class => AuthProvider::class,
      \Hubleto\Framework\Controllers\SignIn::class => Controllers\SignIn::class,
      \Hubleto\Framework\Models\User::class => \Hubleto\App\Community\Auth\Models\User::class,
    ]);

    $this->getService(\Hubleto\Framework\AuthProvider::class)->init();
  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\User::class)->dropTableIfExists()->install();
      $this->getModel(Models\Token::class)->dropTableIfExists()->install();
      $this->getModel(Models\UserHasToken::class)->dropTableIfExists()->install();
      $this->getModel(Models\UserRole::class)->dropTableIfExists()->install();
      $this->getModel(Models\UserHasRole::class)->dropTableIfExists()->install();

      $mUserRole = $this->getModel(Models\UserRole::class);
      $mUserRole->record->recordCreate([
        'id' => Models\UserRole::ROLE_ADMINISTRATOR,
        'role' => 'Administrator',
        'description' => 'Can do anything.',
        'grant_all' => true,
        'is_default' => true,
      ])['id'];
      $mUserRole->record->recordCreate([
        'id' => Models\UserRole::ROLE_CHIEF_OFFICER,
        'role' => 'Chief Officer (default permissions)',
        'description' => 'Can read all data and can modify most of the data. Does not have access to settings.',
        'grant_all' => false,
        'is_default' => true,
      ])['id'];
      $mUserRole->record->recordCreate([
        'id' => Models\UserRole::ROLE_MANAGER,
        'role' => 'Manager (default permissions)',
        'description' => 'Can read and modify all data that he/she owns or is manager.',
        'grant_all' => false,
        'is_default' => true,
      ])['id'];
      $mUserRole->record->recordCreate([
        'id' => Models\UserRole::ROLE_EMPLOYEE,
        'role' => 'Employee (default permissions)',
        'description' => 'In general, can read or modify only data that he/she owns.',
        'grant_all' => false,
        'is_default' => true,
      ])['id'];
      $mUserRole->record->recordCreate([
        'id' => Models\UserRole::ROLE_ASSISTANT,
        'role' => 'Assistant (default permissions)',
        'description' => 'Very similar to employee, but may be more limited in some certain situations.',
        'grant_all' => false,
        'is_default' => true,
      ])['id'];
      $mUserRole->record->recordCreate([
        'id' => Models\UserRole::ROLE_EXTERNAL,
        'role' => 'External (default permissions)',
        'description' => 'By default should not have access to anything. Access permissions must be enabled in settings by administrator.',
        'grant_all' => false,
        'is_default' => true,
      ])['id'];

    }
  }

}
