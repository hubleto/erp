<?php

// load application class
require(__DIR__ . "/../LoadApp.php");

// render
$app = new CeremonyCrmApp($config);

$mUser = new \CeremonyCrmApp\Modules\Core\Settings\Models\User($app);
$origUser1 = $mUser->eloquent->find(1)->toArray();
$adminEmail = $origUser1['email'] ?? 'user@example.com';
$adminPassword = 'abcd';

$app->install();

$mUser = new \CeremonyCrmApp\Modules\Core\Settings\Models\User($app);
$idUserAdministrator = $mUser->eloquent->create([
  'login' => $adminEmail,
  'password' => $mUser->hashPassword($adminPassword),
  'email' => $adminEmail,
  'is_active' => 1,
])->id;

$mUserRole = new \CeremonyCrmApp\Modules\Core\Settings\Models\UserRole($app);
$idRoleAdministrator = $mUserRole->eloquent->create(['name' => 'Administrator'])->id;

$mUserHasRole = new \CeremonyCrmApp\Modules\Core\Settings\Models\UserHasRole($app);
$mUserHasRole->eloquent->create(['id_user' => $idUserAdministrator, 'id_role' => $idRoleAdministrator])->id;

array_walk($app->getRegisteredModules(), function($moduleClass) use($app) {
  $module = new $moduleClass($app);
  $module->generateTestData();
});

echo "New admin credentials: {$adminEmail} / {$adminPassword}";