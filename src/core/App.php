<?php

namespace HubletoMain\Core;

class App {
  public \HubletoMain $main;
  public \HubletoMain\Cli\Agent\Loader|null $cli;

  /**
  * @var array<string>
  */
  protected array $registeredModels = [];

  public string $rootFolder = '';
  public string $namespace = '';

  public string $translationRootContext = '';
  public string $translationContext = '';

  public static function canBeAdded(\HubletoMain $main): bool
  {
    return true;
  }

  public function __construct(\HubletoMain $main)
  {
    $reflection = new \ReflectionClass($this);

    $this->main = $main;
    $this->cli = null;
    $this->rootFolder = pathinfo((string) $reflection->getFilename(), PATHINFO_DIRNAME);
    $this->namespace = $reflection->getNamespaceName();
    $this->translationRootContext = str_replace('.loader', '', strtolower(str_replace('\\', '.', $reflection->getName())));
    $this->translationContext = $this->translationRootContext . '.loader';

    $this->viewNamespace = $this->namespace;
    $this->viewNamespace = str_replace('\\', ':', $this->viewNamespace);

  }

  public function init(): void
  {
    $this->main->addTwigViewNamespace($this->rootFolder . '/Views', $this->viewNamespace);
  }

  public function setCli(\HubletoMain\Cli\Agent\Loader $cli): void
  {
    $this->cli = $cli;
  }

  public function createTestInstance(string $test): \HubletoMain\Core\AppTest
  {
    $reflection = new \ReflectionClass($this);
    $testClass = $reflection->getNamespaceName() . '\\Tests\\' . $test;
    return new $testClass($this, $this->cli); // @phpstan-ignore-line
  }

  public function test(string $test): void
  {
    ($this->createTestInstance($test))->run();
  }

  /** @return array<string> */
  public function getAllTests(): array
  {
    $tests = [];
    $testFiles = (array) @scandir($this->rootFolder . '/Tests');
    foreach ($testFiles as $testFile) {
      if (substr($testFile, -4) == '.php') {
        $tests[] = substr($testFile, 0, -4);
      }
    }

    return $tests;
  }

  /**
  * @return array|array<string, array<string, string>>
  */
  public function loadDictionary(string $language): array
  {

    $dict = [];

    if (strlen($language) == 2) {
      $dictFilename = $this->rootFolder . '/Lang/' . $language . '.json';
      if (is_file($dictFilename)) $dict = (array) @json_decode((string) file_get_contents($dictFilename), true);
      // if (!is_array($dict)) throw new \Exception("Dictionary file {$dictFilename} could not be loaded.");
    }

    return $dict;
  }

  public function translate(string $string, array $vars = []): string
  {
    return $this->main->translate($string, $vars, $this->translationContext);
  }

  public function registerModel(string $model): void
  {
    if (!in_array($model, $this->registeredModels)) {
      $this->registeredModels[] = $model;
    }
  }

  /**
  * @return array<string>
  */
  public function getRegisteredModels(): array
  {
    return $this->registeredModels;
  }

  public function installTables(): void
  {
    // to be overriden
  }

  public function installDefaultPermissions(): void
  {
    // to be overriden
  }
}