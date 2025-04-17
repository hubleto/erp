<?php

namespace HubletoMain\Core;

class LogJavascriptError extends \HubletoMain\Core\Controllers\Controller
{
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): array
  {
    $logDir = $this->main->config->getAsString('logDir');
    $errorRoute = $this->main->urlParamAsString('errorRoute');
    $errors = $this->main->urlParamAsArray('errors');

    if (!is_dir($logDir)) {
      @mkdir($logDir);
    }

    $msg = 
      "---------------------------------------------------------\n"
      . date('Y-m-d H:i:s') . ' ' . $errorRoute
    ;
    foreach ($errors as $error) {
      $msg .= "\n   " . trim($error);
    }

    @file_put_contents($logDir . '/javascript-errors.log', $msg . "\n");

    return [
      'errorRoute' => $errorRoute,
      'errors' => $errors
    ];
  }

}
