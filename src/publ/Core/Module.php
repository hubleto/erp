<?php

namespace CeremonyCrmApp\Core;

class Module {
  public \CeremonyCrmApp $app;
  protected array $registeredModels = [];
  public string $translationContext = '';

  public function __construct(\CeremonyCrmApp $app)
  {
    $this->app = $app;
  }

  public function init(): void
  {
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