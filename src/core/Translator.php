<?php

namespace HubletoMain\Core;

class Translator extends \ADIOS\Core\Translator {
  public \HubletoMain $main;

  public function __construct(\HubletoMain $app)
  {
    $this->main = $app;
    parent::__construct($app);
    $this->dictionary = [];
  }

  // public function getDictionaryFilename(string $context): string
  // {
  //   $dictionaryFilename = '';

  //   $language = $this->main->getLanguage();

  //   foreach ($this->main->apps->getEnabledApps() as $app) {
  //     if (str_starts_with($context, $app->fullName)) {
  //       $dictionaryFilename = $app->rootFolder . '/Lang/' . $language . '.json';
  //     }
  //   }

  //   if (empty($dictionaryFilename)) $dictionaryFilename = parent::getDictionaryFilename($context, $language);

  //   return $dictionaryFilename;
  // }

//   public function addToDictionary(string $string, string $context, string $toLanguage = ''): void
//   {
//     if (empty($toLanguage)) $toLanguage = $this->main->getLanguage();
// var_dump($context);
//     list($class, $classContext) = explode('::', $context);

//     $dictionaryFilename = '';
//     if ($class == \HubletoMain\Core::class) {
//       $dictionaryFilename = __DIR__ . '/../../lang/' . $language . '.json';
//     } else if (str_starts_with($class, 'HubletoApp')) {
//       $app = $this->main->apps->getAppInstance($class);
//       $dictionaryFilename = $app->rootFolder . '/lang/' . $language . '.json';
//     }

//     $this->dictionary[$context][$string] = ''; // @phpstan-ignore-line

//     if (!empty($dictionaryFilename) && is_file($dictionaryFilename)) {
//       $dictionaryFiltered = [];
//       foreach ($this->dictionary as $key => $value) {
//         if (str_starts_with($key, $class . '.')) {
//           $dictionaryFiltered[str_replace($class . '::', '', $key)] = $value;
//         }
//       }

//       // var_dump($dictionaryFiltered);exit;

//       file_put_contents(
//         $dictionaryFilename,
//         json_encode(
//           $dictionaryFiltered,
//           JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
//         )
//       );
//     }
//   }

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
    if (empty($language)) $language = $this->main->getLanguage();
    if ($language == 'en') return [];

    $dictionary = ['lang' => $language];

    if (strlen($language) == 2) {
      $dictFilename = __DIR__ . '/../../lang/' . $language . '.json';
      if (is_file($dictFilename)) {
        $dictionary['HubletoMain\\Core'] = $this->loadDictionaryFromJsonFile($dictFilename);
      }
    }

    if (isset($this->main->apps)) {
      foreach ($this->main->apps->getEnabledApps() as $app) {
        $appDict = $app->loadDictionary($language);
        foreach ($appDict as $key => $value) {
          $dictionary[$app->fullName][(string) $key] = $value;
        }
      }
    }

    $dictionary['ADIOS\\Core\\Loader'] = \ADIOS\Core\Loader::loadDictionary($language);

    return $dictionary;
  }

  public function loadDictionaryForContext(string $language, string $contextFileRef): array
  {
    $dictionaryFilename = '';

    if ($contextFileRef == 'HubletoMain') {
      $dictionaryFilename = __DIR__ . '/../../lang/' . $language . '.json';
    } else if (str_starts_with($contextFileRef, 'HubletoApp')) {
      $appClass = str_replace('/', '\\', $contextFileRef);
  
      $app = $this->main->apps->getAppInstance($appClass);
      if ($app) {
        $dictionaryFilename = $app->rootFolder . '/Lang/' . $language . '.json';
      }
    }

    if (!empty($dictionaryFilename) && is_file($dictionaryFilename)) {
      $dictionary = (array) @json_decode((string) file_get_contents($dictionaryFilename), true);
      return $dictionary;
    } else {
      return [];
    }
  }

  /**
  * @param array<string, string> $vars
  */
  public function translate(string $string, array $vars = [], string $context = "HubletoMain::root", string $toLanguage = ""): string
  {
    if (empty($toLanguage)) $toLanguage = $this->main->getLanguage();
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
      } else if (class_exists($contextClass)) {
        $contextClass::addToDictionary($toLanguage, $contextInner, $string);
      }

      if (empty($translated)) {
        $translated = 'translate(' . $context . '; ' . $string . ')';
      }
    }

    if (empty($translated)) $translated = $string;

    foreach ($vars as $varName => $varValue) {
      $translated = str_replace('{{ ' . $varName . ' }}', $varValue, $translated);
    }

    return $translated;
  }

}