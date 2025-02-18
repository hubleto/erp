<?php

namespace HubletoMain\Core;

class App {

  const DEFAULT_INSTALLATION_CONFIG = [
    'sidebarOrder' => 500,
  ];

  public \HubletoMain $main;
  public \HubletoMain\Cli\Agent\Loader|null $cli;

  /**
  * @var array<string>
  */
  protected array $registeredModels = [];

  public array $manifest = [];

  public string $rootFolder = '';
  public string $viewNamespace = '';
  public string $namespace = '';
  public string $fullName = '';

  public \HubletoMain\Core\Sidebar $sidebar;

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
    $this->fullName = $reflection->getName();
    $this->translationContext = trim(str_replace('\\', '/', $this->fullName), '/');

    $this->viewNamespace = $this->namespace;
    $this->viewNamespace = str_replace('\\', ':', $this->viewNamespace);

    $this->sidebar = new \HubletoMain\Core\Sidebar($this->main);

    $manifestFile = $this->rootFolder . '/manifest.yaml';
    if (is_file($manifestFile)) $this->manifest = (array) \Symfony\Component\Yaml\Yaml::parse((string) file_get_contents($manifestFile));
    else $this->manifest = [];

    $this->validateManifest();

  }

  public function validateManifest() {
    if (empty($this->manifest['name'])) throw new \Exception("{$this->fullName}: Manifest does not contain 'name'.");
  }

  public function init(): void
  {
    $this->manifest['nameTranslated'] = $this->translate($this->manifest['name'], [], 'manifest');
    $this->main->addTwigViewNamespace($this->rootFolder . '/Views', $this->viewNamespace);
  }

  public function getRootUrlSlug(): string {
    return $this->manifest['rootUrlSlug'] ?? '';
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

  public static function getDictionaryFilename(string $language): string
  {
    $appClass = get_called_class();
    $reflection = new \ReflectionClass(get_called_class());
    $rootFolder = pathinfo((string) $reflection->getFilename(), PATHINFO_DIRNAME);
    return $rootFolder . '/Lang/' . $language . '.json';
  }

  /**
  * @return array|array<string, array<string, string>>
  */
  public static function loadDictionary(string $language): array
  {
    $dict = [];
    if (strlen($language) == 2) {
      $dictFilename = static::getDictionaryFilename($language);
      if (is_file($dictFilename)) $dict = (array) @json_decode((string) file_get_contents($dictFilename), true);
    }
    return $dict;
  }

  public function translate(string $string, array $vars = [], string $context = 'root'): string
  {
    return $this->main->translate($string, $vars, $this->fullName . '::' . $context);
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

  public function getFullConfigPath(string $path): string
  {
    return 'apps/' . $this->main->appManager->getAppNameForConfig($this->fullName) . '/' . $path;
  }

  public function configAsString(string $path, string $defaultValue = ''): string
  {
    return (string) $this->main->getConfig($this->getFullConfigPath($path), $defaultValue);
  }

  public function configAsInteger(string $path, int $defaultValue = 0): int
  {
    return (int) $this->main->getConfig($this->getFullConfigPath($path), $defaultValue);
  }

  public function configAsFloat(string $path, float $defaultValue = 0): float
  {
    return (float) $this->main->getConfig($this->getFullConfigPath($path), $defaultValue);
  }

  public function configAsBool(string $path, bool $defaultValue = false): bool
  {
    return (bool) $this->main->getConfig($this->getFullConfigPath($path), $defaultValue);
  }

  public function configAsArray(string $path, array $defaultValue = []): array
  {
    return (array) $this->getConfig($path, $defaultValue);
  }

  public function setConfigAsString(string $path, string $value = ''): void
  {
    $this->main->setConfig($this->getFullConfigPath($path), $value);
  }

  public function setConfigAsInteger(string $path, int $value = 0): void
  {
    $this->main->setConfig($this->getFullConfigPath($path), $value);
  }

  public function setConfigAsFloat(string $path, float $value = 0): void
  {
    $this->main->setConfig($this->getFullConfigPath($path), $value);
  }

  public function setConfigAsBool(string $path, bool $value = false): void
  {
    $this->main->setConfig($this->getFullConfigPath($path), $value);
  }

  public function setConfigAsArray(string $path, array $value = []): void
  {
    $this->main->setConfig($this->getFullConfigPath($path), $value);
  }

}