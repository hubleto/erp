<?php

namespace HubletoCore\Core;

class Translator extends \ADIOS\Core\Translator {


  public function getRootContext(string $context): string
  {
    foreach ($this->app->getModules() as $module) {
      if (empty($module->translationRootContext)) continue;
      if (str_starts_with($context, $module->translationRootContext)) {
        return $module->translationRootContext;
      }
    }

    return '';
  }

  public function getDictionaryFilename(string $context, string $language = ''): string
  {
    $dictionaryFilename = '';

    if (empty($language)) $language = $this->app->config['language'] ?? 'en';
    if (empty($language)) $language = 'en';

    foreach ($this->app->getModules() as $module) {
      if (empty($module->translationRootContext)) continue;
      if (str_starts_with($context, $module->translationRootContext)) {
        $dictionaryFilename = $module->rootFolder . '/Lang/' . $language . '.json';
      }
    }

    if (empty($dictionaryFilename)) $dictionaryFilename = parent::getDictionaryFilename($context, $language);

    return $dictionaryFilename;
  }

  public function addToDictionary(string $string, string $context, string $toLanguage) {
    $dictionaryFile = $this->getDictionaryFilename($context, $toLanguage);
    $rootContext = $this->getRootContext($context);
    $this->dictionary[$context][$string] = '';

    if (!empty($dictionaryFile) && is_file($dictionaryFile)) {
      $dictionaryFiltered = [];
      foreach ($this->dictionary as $key => $value) {
        if (str_starts_with($key, $rootContext . '.')) {
          $dictionaryFiltered[str_replace($rootContext . '.', '', $key)] = $value;
        }
      }

      // var_dump($dictionaryFiltered);exit;

      file_put_contents(
        $dictionaryFile,
        json_encode(
          $dictionaryFiltered,
          JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        )
      );
    }
  }

  public function loadDictionary(string $language = ""): array
  {
    $dictionary = [];

    if (strlen($language) == 2) {
      $dictFilename = __DIR__ . '/../../lang/' . $language . '.json';
      if (is_file($dictFilename)) $dictionary = @json_decode(file_get_contents($dictFilename), true);
    }

    foreach ($this->app->getModules() as $module) {
      $mDict = $module->loadDictionary($language);
      foreach ($mDict as $key => $value) {
        $dictionary[$module->translationRootContext . '.' . $key] = $value;
      }
    }

    // var_dump($dictionary);exit;

    return $dictionary;
  }

  public function translate(string $string, array $vars = [], string $context = "core", $toLanguage = ""): string
  {
    if (empty($toLanguage)) {
      $toLanguage = $this->app->config['language'] ?? "en";
    }

    if ($toLanguage == "en") {
      $translated = $string;
    } else {
      if (empty($this->dictionary)) {
        $this->dictionary = $this->loadDictionary($toLanguage);
      }

      $translated = '';

      if (isset($this->dictionary[$context][$string])) {
        $translated = $this->dictionary[$context][$string];
      }

      if (empty($translated) && $toLanguage != 'en') {
        $translated = $string;
        $this->addToDictionary($string, $context, $toLanguage);
      }
    }

    if (empty($translated)) $translated = $string;

    foreach ($vars as $varName => $varValue) {
      $translated = str_replace('{{ ' . $varName . ' }}', $varValue, $translated);
    }

    return $translated;
  }

}