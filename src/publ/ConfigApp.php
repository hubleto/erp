<?php

$config['appNamespace']                  = 'CeremonyCrmApp';

// session
$config['sessionSalt'] = 'CeremonyCrmApp';

// widgets
$config['widgets'] = [];

// misc
// $config['defaultController'] = 'CeremonyCrmApp/Controllers/App/Dashboard';

$config['coreClasses'] = [
  'Core\\Permissions' => \CeremonyCrmApp\Core\Permissions::class,
  'Core\\Router' => \CeremonyCrmApp\Core\Router::class,
  'Core\\Controller' => \CeremonyCrmApp\Core\Controller::class,
  'Models\\User' => \CeremonyCrmApp\Modules\Core\Settings\Models\User::class,
  'Models\\UserRole' => \CeremonyCrmApp\Modules\Core\Settings\Models\UserRole::class,
  'Models\\UserHasRole' => \CeremonyCrmApp\Modules\Core\Settings\Models\UserHasRole::class,
  'Controllers\\Desktop' => \CeremonyCrmApp\Controllers\Desktop::class,
// Uncomment these lines if you want to override another core classes
//   'Core\\Console' => \PortalApp\Core\Console::class,
//   'Core\\Locale' => \PortalApp\Core\Locale::class,
//   'Core\\TwigLoader' => \PortalApp\Core\TwigLoader::class,
//   'Core\\UserNotifications' => \PortalApp\Core\UserNotifications::class,
//   'Core\\Email' => \PortalApp\Core\Email::class,
//   'Models\\User' => \PortalApp\Models\User::class,
//   'Core\\ViewWithController' => \PortalApp\Core\ViewWithController::class,
];

$config['auth'] = [
  'provider' => \ADIOS\Auth\ModelUser::class,
];
