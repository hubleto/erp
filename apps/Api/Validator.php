<?php

namespace Hubleto\App\Community\Api;

class Validator extends \Hubleto\Erp\Core
{

  public function getFullControllerClassName(string $app, string $controller): string
  {
    return $app . '\\Controllers\\Api\\' . $controller;
  }

  public function validateAppAndController(string $app, string $controller): void
  {
    if (!class_exists($this->getFullControllerClassName($app, $controller))) {
      throw new \Exception('Unknown app or controller.');
    }
  }

  public function validateApiKey(string $app, string $controller, string $key): void
  {
    /** @var Models\Key */
    $mKey = $this->getModel(Models\Key::class);

    $key = $mKey->record->with('PERMISSIONS')->where('key', $key)->first();
    if (!$key) throw new \Exception('Unknown key.');
    if (strtotime($key->valid_until) < strtotime("now")) throw new \Exception('Key validity expired.');
    if (!$key->is_enabled) throw new \Exception('Key is not enabled.');
    // TODO: IP whitelist/blacklist validation

    $hasPermission = false;
    foreach ($key->PERMISSIONS as $permission) {
      if ($permission->app == $app && $permission->controller == $controller) {
        $hasPermission = true;
        break;
      }
    }

    if (!$hasPermission) throw new \Exception('Not enough permissions.');


  }

}