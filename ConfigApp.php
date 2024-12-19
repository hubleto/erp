<?php

$config['appNamespace'] = 'CeremonyCrmApp';
$config['sessionSalt'] = 'CeremonyCrmApp';

$config['coreClasses'] = [
  'Core/Permissions' => \CeremonyCrmApp\Core\Permissions::class,
  'Core/Router' => \CeremonyCrmApp\Core\Router::class,
  'Core/Controller' => \CeremonyCrmApp\Core\Controller::class,
  'Models/User' => \CeremonyCrmMod\Settings\Models\User::class,
  'Models/UserRole' => \CeremonyCrmMod\Settings\Models\UserRole::class,
  'Models/UserHasRole' => \CeremonyCrmMod\Settings\Models\UserHasRole::class,
  'Controllers/Desktop' => \CeremonyCrmApp\Controllers\Desktop::class,
];

$config['db'] = [
  'provider' => \ADIOS\Core\Db\Providers\MySQLi::class,
];

$config['auth'] = [
  'provider' => \CeremonyCrmApp\Core\Auth::class,
];
