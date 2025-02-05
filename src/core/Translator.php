<?php

namespace HubletoMain\Core;

class Translator extends \ADIOS\Core\Translator {
  public \HubletoMain $main;

  public function __construct(\HubletoMain $app)
  {
    parent::__construct($app);
    $this->main = $app;
  }


  public function getRootContext(string $context): string
  {
    foreach ($this->main->appManager->getRegisteredApps() as $app) {
      if (empty($app->translationRootContext)) continue;
      if (str_starts_with($context, $app->translationRootContext)) {
        return $app->translationRootContext;
      }
    }

    return '';
  }

  public function getDictionaryFilename(string $context, string $language = ''): string
  {
    $dictionaryFilename = '';

    if (empty($language)) $language = $this->main->configAsString('language', 'en');
    if (empty($language)) $language = 'en';

    foreach ($this->main->appManager->getRegisteredApps() as $app) {
      if (empty($app->translationRootContext)) continue;
      if (str_starts_with($context, $app->translationRootContext)) {
        $dictionaryFilename = $app->rootFolder . '/Lang/' . $language . '.json';
      }
    }

    if (empty($dictionaryFilename)) $dictionaryFilename = parent::getDictionaryFilename($context, $language);

    return $dictionaryFilename;
  }

  public function addToDictionary(string $string, string $context, string $toLanguage): void
  {
    $dictionaryFile = $this->getDictionaryFilename($context, $toLanguage);
    $rootContext = $this->getRootContext($context);

    $this->dictionary[$context][$string] = ''; // @phpstan-ignore-line

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

  /**
  * @return array|array<string, array<string, string>>
  */
  public function loadDictionaryFromJsonFile(string $jsonFile): array
  {
    return (array) @json_decode((string) file_get_contents($jsonFile), true);
  }

  /**
  * @return array|array<string, array<string, string>>
  */
  public function loadDictionary(string $language = ""): array
  {
    $dictionary = [];

    if (strlen($language) == 2) {
      $dictFilename = __DIR__ . '/../../lang/' . $language . '.json';
      if (is_file($dictFilename)) {
        $dictionary = $this->loadDictionaryFromJsonFile($dictFilename);
      }
    }

    foreach ($this->main->appManager->getRegisteredApps() as $app) {
      $mDict = $app->loadDictionary($language);
      foreach ($mDict as $key => $value) {
        $dictionary[$app->translationRootContext . '.' . (string) $key] = $value;
      }
    }

    return $dictionary;
  }

  /**
  * @param array<string, string> $vars
  */
  public function translate(string $string, array $vars = [], string $context = "core", string $toLanguage = ""): string
  {
    if (empty($toLanguage)) {
      $toLanguage = $this->main->configAsString('language', 'en');
    }

    if ($toLanguage == 'en') {
      $translated = $string;
    } else {
      if (empty($this->dictionary)) {
        $this->dictionary = $this->loadDictionary($toLanguage);
      }

      $translated = '';

      if (isset($this->dictionary[$context][$string])) { // @phpstan-ignore-line
        $translated = (string) $this->dictionary[$context][$string];
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