<?php

namespace Hubleto\App\Community\AiAssistent;

use Hubleto\Framework\Interfaces\AppInterface;

class GeminiProvider
{
  private AppInterface $app;
  private string $apiKey;

  public function __construct(AppInterface $app)
  {
    $this->app = $app;
    $this->apiKey = $app->configAsString('apiKey') ?: '';
  }

  public function sendMessage(string $message, array $history = [], string $mode = 'user', array $contextData = []): string
  {
    if (empty($this->apiKey)) {
      throw new \Exception("Gemini API key is not configured. Please set it in the application settings.");
    }

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key=" . $this->apiKey;

    if ($mode === 'developer') {
      $systemPrompt = "You are 'Hubi', an intelligent technical assistant operating within the Hubleto ERP ecosystem. Your goal is to help developers write code, create models, and build extensions for Hubleto. When answering, actively use Google Search to find relevant technical information and code examples, focusing exclusively on these official sources: https://developer.hubleto.eu/v0/docs/ (Developer Documentation), and https://community.hubleto.eu/ (Community updates, bug fixes, and feature discussions). Provide accurate, deeply technical, and concise answers based on these sources.";
    } else {
      $systemPrompt = "You are 'Hubi', an intelligent assistant operating within the Hubleto ERP ecosystem. Your primary goal is to help regular users effectively use and navigate Hubleto features like CRM, Invoicing, etc. When answering, actively use Google Search to find relevant user-facing information, focusing exclusively on these official sources: https://help.hubleto.eu/v0/en/user-guide/ (User Guide), and https://community.hubleto.eu/ (Community updates and discussions). Always provide accurate, helpful, and non-technical answers based on these sources.";
    }

    if (!empty($contextData)) {
      $systemPrompt .= "\n\nImportant Context: The user is currently viewing or asking about a specific record in the ERP. Here is the relevant data for this record:\n" . json_encode($contextData, JSON_PRETTY_PRINT) . "\nPlease use this data to provide a highly detailed and context-aware answer.";
    }

    $contents = [];
    foreach ($history as $msg) {
      if (isset($msg['role']) && isset($msg['content'])) {
        $contents[] = [
          'role' => $msg['role'] === 'user' ? 'user' : 'model',
          'parts' => [['text' => $msg['content']]]
        ];
      }
    }

    $contents[] = [
      'role' => 'user',
      'parts' => [['text' => $message]]
    ];

    $payload = [
      'systemInstruction' => [
        'parts' => [['text' => $systemPrompt]]
      ],
      'contents' => $contents,
      'tools' => [
        [
          'googleSearch' => new \stdClass()
        ]
      ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode >= 400) {
      throw new \Exception("Communication error with Google Gemini API (HTTP $httpCode): " . $response);
    }

    $responseData = json_decode((string)$response, true);
    
    if (isset($responseData['candidates'][0]['content']['parts'])) {
      $fullText = '';
      foreach ($responseData['candidates'][0]['content']['parts'] as $part) {
        if (isset($part['text'])) {
          $fullText .= $part['text'];
        }
      }
      if (!empty($fullText)) {
        return $fullText;
      }
    }

    return "Failed to get a response from AIAssistant.";
  }
}
