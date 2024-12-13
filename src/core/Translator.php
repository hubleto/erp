<?php

namespace CeremonyCrmApp\Core;

class Translator extends \ADIOS\Core\Translator {


  private function getDictionaryFilename(string $context, string $language = ''): string
  {
    $dictionaryFilename = '';

    if (empty($language)) $language = $this->app->config['language'] ?? 'en';
    if (empty($language)) $language = 'en';

    foreach ($this->app->getModules() as $module) {
      if (str_starts_with($context, $module->translationRootContext)) {
        $dictionaryFilename = $module->rootFolder . '/lang/' . $language . '.json';
      }
    }

    if (empty($dictionaryFilename)) $dictionaryFilename = parent::getDictionaryFilename($context, $language);

    return $dictionaryFilename;
  }

  public function loadDictionary(string $language = ""): array
  {
    $dictionary = [];

    if (strlen($language) == 2) {
      $dictFilename = __DIR__ . '/../../lang/' . $language . '.json';
      if (is_file($dictFilename)) $dictionary = @json_decode(file_get_contents($dictFilename), true);
    }

    foreach ($this->app->getModules() as $module) {
      $dictionary = array_merge($dictionary, $module->loadDictionary($language));
    }

    return $dictionary;
  }
}