<?php

$config['appNamespace'] = 'CeremonyCrmApp';

// session
$config['sessionSalt'] = 'CeremonyCrmApp';

// widgets
$config['widgets'] = [];

// misc
// $config['defaultController'] = 'CeremonyCrmApp/Controllers/App/Dashboard';

$config['coreClasses'] = [
  'Core/Permissions' => \CeremonyCrmApp\Core\Permissions::class,
  'Core/Router' => \CeremonyCrmApp\Core\Router::class,
  'Core/Controller' => \CeremonyCrmApp\Core\Controller::class,
//   'Core/Console' => \PortalApp\Core\Console::class,
//   'Core/Locale' => \PortalApp\Core\Locale::class,
//   'Core/TwigLoader' => \PortalApp\Core\TwigLoader::class,
//   'Core/UserNotifications' => \PortalApp\Core\UserNotifications::class,
  'Models/User' => \CeremonyCrmMod\Core\Settings\Models\User::class,
  'Models/UserRole' => \CeremonyCrmMod\Core\Settings\Models\UserRole::class,
  'Models/UserHasRole' => \CeremonyCrmMod\Core\Settings\Models\UserHasRole::class,
  'Controllers/Desktop' => \CeremonyCrmApp\Controllers\Desktop::class,
];

$config['db'] = [
  'provider' => \ADIOS\Core\Db\Providers\MySQLi::class,
];

$config['auth'] = [
  'provider' => \CeremonyCrmApp\Core\Auth::class,
];
