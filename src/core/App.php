<?php

namespace HubletoMain\Core;

class App {
  public \HubletoMain $main;
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
    $this->rootFolder = pathinfo($reflection->getFilename(), PATHINFO_DIRNAME);
    $this->namespace = $reflection->getNamespaceName();
    $this->translationRootContext = str_replace('.loader', '', strtolower(str_replace('\\', '.', $reflection->getName())));
    $this->translationContext = $this->translationRootContext . '.loader';
  }

  public function init(): void
  {
  }

  public function test(\HubletoMain\Cli\Agent\Loader $cli, string $test): void
  {
    $reflection = new \ReflectionClass($this);
    $testClass = $reflection->getNamespaceName() . '\\Tests\\' . $test;
    (new $testClass($this, $cli))->run();
  }

  /**
  * @return array<string, array<string, string>>
  */
  public function loadDictionary(string $language): array
  {

    $dict = [];

    if (strlen($language) == 2) {
      $dictFilename = $this->rootFolder . '/Lang/' . $language . '.json';
      if (is_file($dictFilename)) $dict = json_decode(file_get_contents($dictFilename), true);
      if (!is_array($dict)) throw new \Exception("Dictionary file {$dictFilename} could not be loaded.");
    }

    return $dict;
  }

  public function translate(string $string, array $vars = []): string
  {
    return $this->main->translate($string, $vars, $this->translationContext);
  }

  public function registerModel(string $model)
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

  public function installTables()
  {
    // to be overriden
  }

  public function installDefaultPermissions()
  {
    // to be overriden
  }
}