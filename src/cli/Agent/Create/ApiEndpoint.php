<?php

namespace HubletoMain\Cli\Agent\Create;

class ApiEndpoint extends \HubletoMain\Cli\Agent\Command
{

  public function run(): void
  {
    $appNamespace = (string) ($this->arguments[3] ?? '');
    $endpoint = (string) ($this->arguments[4] ?? '');
    $force = (bool) ($this->arguments[5] ?? false);

    $this->main->apps->init();

    if (empty($appNamespace)) throw new \Exception("<appNamespace> not provided.");
    if (empty($endpoint)) throw new \Exception("<endpoint> not provided.");

    $endpointPascalCase = \ADIOS\Core\Helper::kebabToPascal($endpoint);

    $app = $this->main->apps->getAppInstance($appNamespace);

    if (!$app) throw new \Exception("App '{$appNamespace}' does not exist or is not installed.");

    $rootFolder = $app->rootFolder;

    if (is_file($rootFolder . '/Controllers/Api/' . $endpointPascalCase . '.php') && !$force) {
      throw new \Exception("REST API endpoint '{$endpoint}' already exists in app '{$appNamespace}'. Use 'force' to overwrite existing files.");
    }

    $tplFolder = __DIR__ . '/../../../code_templates/snippets';
    $this->main->addTwigViewNamespace($tplFolder, 'snippets');

    $tplVars = [
      'appNamespace' => $appNamespace,
      'endpoint' => $endpoint,
      'endpointPascalCase' => $endpointPascalCase,
    ];

    if (!is_dir($rootFolder . '/Controllers')) mkdir($rootFolder . '/Controllers');
    if (!is_dir($rootFolder . '/Controllers/Api')) mkdir($rootFolder . '/Controllers/Api');
    file_put_contents($rootFolder . '/Controllers/Api/' . $endpointPascalCase . '.php', $this->main->twig->render('@snippets/ApiController.php.twig', $tplVars));

    $this->cli->white("\n");
    $this->cli->cyan("REST API endpoint '{$endpoint}' in '{$appNamespace}' created successfully.\n");
    $this->cli->yellow("⚠  NEXT STEPS:\n");
    $this->cli->yellow("⚠  -> Add the route in the `init()` method of {$app->rootFolder}/Loader.php\n");
    $this->cli->colored("cyan", "black", "Add to Loader.php->init(): \$this->main->router->httpGet([ '/^{$app->manifest['rootUrlSlug']}\/api\/{$endpoint}\/?$/' => Controllers\\Api\\{$endpointPascalCase}::class ]);");
    $this->cli->yellow("\n");
    $this->cli->yellow("⚠  -> Check the endpoint\n");
    $this->cli->colored("cyan", "black", "Open in browser: {$this->main->config->getAsString('rootUrl')}/{$app->manifest['rootUrlSlug']}/api/{$endpoint}");
    $this->cli->yellow("\n");
  }

}