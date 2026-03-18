<?php

namespace Hubleto\App\Community\AiAssistent\Controllers;

use Hubleto\App\Community\AiAssistent\GeminiProvider;

class Chat extends \Hubleto\Erp\Controllers\ApiController
{
  public function response(): array
  {
    $request = json_decode(file_get_contents('php://input'), true);
    $messages = $request['messages'] ?? [];
    $message = $request['message'] ?? '';
    $mode = $request['mode'] ?? 'user';
    $modelParam = $request['model'] ?? null;
    $idParam = $request['id'] ?? null;

    if (empty($message)) {
      throw new \Exception('Empty message.');
    }

    $aiApp = $this->appManager()->getApp(\Hubleto\App\Community\AiAssistent\Loader::class);
    
    $contextData = [];
    $sensitivityLevel = $aiApp->configAsInteger('sensitivityLevel');
    if ($sensitivityLevel > 0 && $modelParam && $idParam) {
      $modelParam = str_replace('/', '\\', $modelParam);
      if (class_exists($modelParam)) {
        $modelObj = $this->getService($modelParam);
        if (method_exists($modelObj, 'getAiAssistantContext')) {
          $contextData = $modelObj->getAiAssistantContext($sensitivityLevel, (int)$idParam);
        }
      }
    }

    $provider = new GeminiProvider($aiApp);
    $response = $provider->sendMessage($message, $messages, $mode, $contextData);

    $parsedown = new \Parsedown();
    $responseHtml = $parsedown->text($response);

    return [
      'status' => 'success',
      'response' => $response,
      'responseHtml' => $responseHtml
    ];
  }
}
