<?php

// $config['appNamespace'] = 'HubletoMain';

$config['defaultSignInView'] = '@hubleto/SignIn.twig';
$config['defaultDesktopView'] = '@hubleto/Desktop.twig';

$config['coreClasses'] = [
  'Core/Permissions' => \HubletoMain\Core\Permissions::class,
  'Core/Router' => \HubletoMain\Core\Router::class,
  'Core/Controller' => \HubletoMain\Core\Controller::class,
  'Models/User' => \HubletoApp\Community\Settings\Models\User::class,
  'Models/UserRole' => \HubletoApp\Community\Settings\Models\UserRole::class,
  'Models/UserHasRole' => \HubletoApp\Community\Settings\Models\UserHasRole::class,
  'Controllers/Desktop' => \HubletoMain\Controllers\Desktop::class,
];

$config['auth'] = [
  'provider' => \HubletoMain\Core\Auth::class,
];
