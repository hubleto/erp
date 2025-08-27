<?php declare(strict_types=1);

namespace Hubleto\Erp\Api;

class LogJavascriptError extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $logFolder = $this->getConfig()->getAsString('logFolder');
    $errorRoute = $this->getRouter()->urlParamAsString('errorRoute');
    $errors = $this->getRouter()->urlParamAsArray('errors');

    if (!is_dir($logFolder)) {
      @mkdir($logFolder);
    }

    $msg =
      "---------------------------------------------------------\n"
      . date('Y-m-d H:i:s') . ' ' . $errorRoute
    ;
    foreach ($errors as $error) {
      $msg .= "\n   " . trim($error);
    }

    @file_put_contents($logFolder . '/javascript-errors.log', $msg . "\n");

    return [
      'errorRoute' => $errorRoute,
      'errors' => $errors
    ];
  }

}
