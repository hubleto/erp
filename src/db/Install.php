<?php

// load configs
require_once(__DIR__ . "/../../ConfigEnv.php");
require_once(__DIR__ . "/../../app/bin/ConfigApp.php");

// load application class
require(__DIR__ . "/../../app/bin/App.php");

try {
  $app = new CeremonyCrmApp($config, TRUE);
  $app->install();

  $mUser = new \CeremonyCrmApp\Modules\Core\Settings\Models\User($app);
  $idUserAdministrator = $mUser->eloquent->create([
    'login' => 'administrator',
    'password' => $mUser->hashPassword('administrator'),
    'is_active' => 1,
  ])->id;

  $mUserRole = new \CeremonyCrmApp\Modules\Core\Settings\Models\UserRole($app);
  $idRoleAdministrator = $mUserRole->eloquent->create(['name' => 'Administrator'])->id;

  $mUserHasRole = new \CeremonyCrmApp\Modules\Core\Settings\Models\UserHasRole($app);
  $mUserHasRole->eloquent->create(['id_user' => $idUserAdministrator, 'id_role' => $idRoleAdministrator])->id;
} catch (\Exception $e) {
  echo $e->getMessage();
}