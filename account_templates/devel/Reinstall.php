<?php

// load application class
require(__DIR__ . "/../LoadApp.php");

// render
$app = new CeremonyCrmApp($config);

$mProfile = new \CeremonyCrmApp\Modules\Core\Settings\Models\Profile($app);
$mUser = new \CeremonyCrmApp\Modules\Core\Settings\Models\User($app);
$mUserRole = new \CeremonyCrmApp\Modules\Core\Settings\Models\UserRole($app);
$mUserHasRole = new \CeremonyCrmApp\Modules\Core\Settings\Models\UserHasRole($app);

$origProfile1 = $mProfile->eloquent->find(1)?->toArray();
$companyName = $origProfile1['company'] ?? '';

$origUser1 = $mUser->eloquent->find(1)?->toArray();
$adminEmail = $origUser1['email'] ?? 'user@example.com';
$adminPassword = 'abcd';

$app->install();

$mProfile->install();
$mUser->install();
$mUserRole->install();
$mUserHasRole->install();

$idProfile = $mProfile->eloquent->create(['company' => $companyName])->id;

$idUserAdministrator = $mUser->eloquent->create([
  'login' => $adminEmail,
  'password' => $mUser->hashPassword($adminPassword),
  'email' => $adminEmail,
  'is_active' => 1,
  'id_active_profile' => $idProfile,
])->id;

$idRoleAdministrator = $mUserRole->eloquent->create(['role' => 'Administrator'])->id;

$mUserHasRole->eloquent->create(['id_user' => $idUserAdministrator, 'id_role' => $idRoleAdministrator])->id;

echo "New admin credentials: {$adminEmail} / {$adminPassword}";