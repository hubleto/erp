<?php declare(strict_types=1);

namespace Hubleto\App\Community\Api\Controllers\Api;

// Example of usage:

// REQUEST FROM 192.168.10.12
//   -> appka tuto ma ulozeny apiKey vo svojom config
//   -> libka HubletoApi->post($app, $controller, $data) najprv zasifruje data a potom posle

// POST accounts/wai-blue/api/call
//   -H 'Content-type: application/json'
//   -H 'Authorization: Bearer {apiKey}'
//   -D '{
//     app: Hubleto\App\Community\Contacts
//     controller: GetContacts
//     vars: {}
//   }'


use Exception;

class Call extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    try {

      $app = $this->router()->urlParamAsString('app');
      $controller = $this->router()->urlParamAsString('controller');
      $vars = $this->router()->urlParamAsArray('vars');

      // TODO: check permissions, IP black/whitelist
      // TODO: if permissions ok, create controller object (namespace = {app} + \Controllers\Api + {controller}), run renderJson, return output
      // TODO: log usage status (error, success)

    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "status" => "success"
    ];
  }
}
