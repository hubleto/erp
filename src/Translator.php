<?php

namespace HubletoMain;

use Hubleto\Framework\Interfaces\AppManagerInterface;

class Translator implements \Hubleto\Framework\Interfaces\TranslatorInterface
{

  public array $dictionary = [];

  public function __construct(public \Hubleto\Framework\Loader $main)
  {
    $this->dictionary = [];
  }

  /**
   * [Description for getAppManager]
   *
   * @return AppManagerInterface
   * 
   */
  public function getAppManager(): AppManagerInterface
  {
    return $this->main->getAppManager();
  }

  public function getDictionaryFilename(string $context, string $language = ''): string
  {
    $dictionaryFile = '';

    if (empty($language)) $language = $this->getConfig()->getAsString('language', 'en');
    if (empty($language)) $language = 'en';

    if (strlen($language) == 2) {
      $dictionaryFile = $this->main->srcFolder . "/Lang/{$language}.json";
    }

    return $dictionaryFile;
  }

  public function addToDictionary(string $string, string $context, string $toLanguage): void
  {
    $dictionaryFile = $this->getDictionaryFilename($context, $toLanguage);
    $this->dictionary[$toLanguage][$context][$string] = '';

    if (is_file($dictionaryFile)) {
      file_put_contents(
        $dictionaryFile,
        json_encode(
          $this->dictionary[$toLanguage],
          JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        )
      );
    }
  }

  /**
  * @return array|array<string, array<string, string>>
  */
  public function loadDictionary(string $language = ""): array
  {
    if (empty($language)) {
      $language = $this->main->getLanguage();
    }

    if ($language == 'en') {
      return [];
    }

    $dictionary = [];

    foreach ($this->getAppManager()->getEnabledApps() as $app) {
      $appDict = $app->loadDictionary($language);
      foreach ($appDict as $key => $value) {
        $dictionary[$app->fullName][(string) $key] = $value;
      }
    }

    $dictionary['HubletoMain\\Loader'] = Loader::loadDictionary($language);

    return $dictionary;
  }

  /**
  * @param array<string, string> $vars
  */
  public function translate(string $string, array $vars = [], string $context = "Hubleto\\Framework\\Loader::root", string $toLanguage = ""): string
  {
    if (empty($toLanguage)) {
      $toLanguage = $this->main->getLanguage();
    }
    if (strpos($context, '::')) {
      list($contextClass, $contextInner) = explode('::', $context);
    } else {
      $contextClass = '';
      $contextInner = $context;
    }

    if ($toLanguage == 'en') {
      $translated = $string;
    } else {
      if (empty($this->dictionary[$contextClass]) && class_exists($contextClass)) {
        $this->dictionary[$contextClass] = $contextClass::loadDictionary($toLanguage);
      }

      $translated = '';

      if (!empty($this->dictionary[$contextClass][$contextInner][$string])) { // @phpstan-ignore-line
        $translated = (string) $this->dictionary[$contextClass][$contextInner][$string];
      } elseif (class_exists($contextClass)) {
        $contextClass::addToDictionary($toLanguage, $contextInner, $string);
      }

      if (empty($translated)) {
        $translated = 'translate(' . $context . '; ' . $string . ')';
      }
    }

    if (empty($translated)) {
      $translated = $string;
    }

    foreach ($vars as $varName => $varValue) {
      $translated = str_replace('{{ ' . $varName . ' }}', $varValue, $translated);
    }

    return $translated;
  }

}
