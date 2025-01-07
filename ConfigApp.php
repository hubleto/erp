<?php

$config['appNamespace'] = 'HubletoMain';
$config['sessionSalt'] = 'HubletoMain';

$config['coreClasses'] = [
  'Core/Permissions' => \HubletoMain\Core\Permissions::class,
  'Core/Router' => \HubletoMain\Core\Router::class,
  'Core/Controller' => \HubletoMain\Core\Controller::class,
  'Models/User' => \HubletoApp\Settings\Models\User::class,
  'Models/UserRole' => \HubletoApp\Settings\Models\UserRole::class,
  'Models/UserHasRole' => \HubletoApp\Settings\Models\UserHasRole::class,
  'Controllers/Desktop' => \HubletoMain\Controllers\Desktop::class,
];

$config['db'] = [
  'provider' => \ADIOS\Core\Db\Providers\MySQLi::class,
];

$config['auth'] = [
  'provider' => \HubletoMain\Core\Auth::class,
];
