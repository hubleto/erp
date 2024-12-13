<?php

namespace CeremonyCrmApp\Core;

class Module {
  public \CeremonyCrmApp $app;
  protected array $registeredModels = [];

  public string $rootFolder = '';

  public string $translationRootContext = '';
  public string $translationContext = '';

  public static function canBeAdded(\CeremonyCrmApp $app): bool
  {
    return true;
  }

  public function __construct(\CeremonyCrmApp $app)
  {
    $reflection = new \ReflectionClass($this);

    $this->app = $app;
    $this->rootFolder = pathinfo($reflection->getFilename(), PATHINFO_DIRNAME);
    $this->translationRootContext = str_replace('.loader', '', strtolower(str_replace('\\', '.', $reflection->getName())));
    $this->translationContext = $this->translationRootContext . '.loader';
  }

  public function init(): void
  {
  }

  public function loadDictionary(string $language): array {

    $dict = [];

    if (strlen($language) == 2) {
      $dictFilename = $this->rootFolder . '/Lang/' . $language . '.json';
      if (is_file($dictFilename)) $dict = @json_decode(file_get_contents($dictFilename), true);
    }

    return $dict;
  }

  public function translate(string $string, array $vars = []): string
  {
    return $this->app->translate($string, $vars, $this->translationContext);
  }

  public function registerModel(string $model)
  {
    if (!in_array($model, $this->registeredModels)) {
      $this->registeredModels[] = $model;
    }
  }

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