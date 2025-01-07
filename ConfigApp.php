<?php

$config['appNamespace'] = 'HubletoCore';
$config['sessionSalt'] = 'HubletoCore';

$config['coreClasses'] = [
  'Core/Permissions' => \HubletoCore\Core\Permissions::class,
  'Core/Router' => \HubletoCore\Core\Router::class,
  'Core/Controller' => \HubletoCore\Core\Controller::class,
  'Models/User' => \HubletoApp\Settings\Models\User::class,
  'Models/UserRole' => \HubletoApp\Settings\Models\UserRole::class,
  'Models/UserHasRole' => \HubletoApp\Settings\Models\UserHasRole::class,
  'Controllers/Desktop' => \HubletoCore\Controllers\Desktop::class,
];

$config['db'] = [
  'provider' => \ADIOS\Core\Db\Providers\MySQLi::class,
];

$config['auth'] = [
  'provider' => \HubletoCore\Core\Auth::class,
];
