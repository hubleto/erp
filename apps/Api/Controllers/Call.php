<?php declare(strict_types=1);

namespace Hubleto\App\Community\Api\Controllers;

// Example of usage:

// REQUEST FROM 192.168.10.12
//   -> appka tuto ma ulozeny apiKey vo svojom config
//   -> libka HubletoApi->post($app, $controller, $data) najprv zasifruje data a potom posle

// POST accounts/wai-blue/api/call
//   -H 'Content-type: application/json'
//   -D '{
//     key: {apiKey}
//     app: Hubleto\App\Community\Contacts
//     controller: GetContacts
//     vars: {}
//   }'

use \Hubleto\App\Community\Api\Validator;
use \Hubleto\App\Community\Api\Models\Usage;
use \Hubleto\App\Community\Api\Models\Key;

class Call extends \Hubleto\Erp\Controllers\ApiController
{
  public bool $requiresAuthenticatedUser = false;

  public function renderJson(): array
  {
    try {

      $key = $this->router()->urlParamAsString('key');
      $app = $this->router()->urlParamAsString('app');
      $controller = $this->router()->urlParamAsString('controller');
      $varsString = $this->router()->urlParamAsString('vars');

      /** @var $validator Validator */
      $validator = $this->getService(Validator::class);

      $validator->validateAppAndController($app, $controller);
      $validator->validateApiKey($app, $controller, $key);

      /** @var $controllerObject \Hubleto\Erp\Controller */
      $controllerObject = $this->getService($validator->getFullControllerClassName($app, $controller));
      $vars = @json_decode($varsString, true);
      $controllerObject->router()->setRouteVars($vars?? []);
      $output = $controllerObject->renderJson();

      /** @var $mKey Key */
      $mKey = $this->getModel(Key::class);
      $key = $mKey->record->where('key', $key)->first();

      /** @var $mUsage Usage */
      $mUsage = $this->getModel(Usage::class);
      $mUsage->record->recordCreate([
        'id_key' => $key->id,
        'app' => $app,
        'controller' => $controller,
        'used_on' => date("Y-m-d H:i:s"),
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'status' => 1,
      ]);

      return $output;

    } catch (\Throwable $e) {
      return [
        "status" => "error",
        "error" => $e->getMessage()
      ];
    }
  }
}
