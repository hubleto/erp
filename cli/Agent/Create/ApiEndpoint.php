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

    if (empty($appNamespace)) {
      throw new \Exception("<appNamespace> not provided.");
    }
    if (empty($endpoint)) {
      throw new \Exception("<endpoint> not provided.");
    }

    $endpointPascalCase = \Hubleto\Framework\Helper::kebabToPascal($endpoint);

    $app = $this->main->apps->getAppInstance($appNamespace);

    if (!$app) {
      throw new \Exception("App '{$appNamespace}' does not exist or is not installed.");
    }

    $rootFolder = $app->rootFolder;

    if (is_file($rootFolder . '/Controllers/Api/' . $endpointPascalCase . '.php') && !$force) {
      throw new \Exception("REST API endpoint '{$endpoint}' already exists in app '{$appNamespace}'. Use 'force' to overwrite existing files.");
    }

    $tplFolder = __DIR__ . '/../../Templates/snippets';
    $this->main->addTwigViewNamespace($tplFolder, 'snippets');

    $tplVars = [
      'appNamespace' => $appNamespace,
      'endpoint' => $endpoint,
      'endpointPascalCase' => $endpointPascalCase,
    ];

    if (!is_dir($rootFolder . '/Controllers')) {
      mkdir($rootFolder . '/Controllers');
    }
    if (!is_dir($rootFolder . '/Controllers/Api')) {
      mkdir($rootFolder . '/Controllers/Api');
    }
    file_put_contents($rootFolder . '/Controllers/Api/' . $endpointPascalCase . '.php', $this->main->twig->render('@snippets/ApiController.php.twig', $tplVars));

    $codeRoute = [ "\$this->main->router->httpGet([ '/^{$app->manifest['rootUrlSlug']}\/api\/{$endpoint}\/?$/' => Controllers\\Api\\{$endpointPascalCase}::class ]);" ];
    $codeRouteInserted = \Hubleto\Terminal::insertCodeToFile($rootFolder . '/Loader.php', '//@hubleto-cli:routes', $codeRoute);

    \Hubleto\Terminal::white("\n");
    \Hubleto\Terminal::cyan("REST API endpoint '{$endpoint}' in '{$appNamespace}' created successfully.\n");

    if (!$codeRouteInserted) {
      \Hubleto\Terminal::yellow("⚠ Failed to add some code automatically\n");
      \Hubleto\Terminal::yellow("⚠  -> Add the route in the `init()` method of {$app->rootFolder}/Loader.php\n");
      \Hubleto\Terminal::colored("cyan", "black", "Add to Loader.php->init():");
      \Hubleto\Terminal::colored("cyan", "black", join("\n", $codeRoute));
      \Hubleto\Terminal::yellow("\n");
    }

    \Hubleto\Terminal::yellow("💡  TIPS:\n");
    \Hubleto\Terminal::yellow("💡  -> Test the endpoint\n");
    \Hubleto\Terminal::colored("cyan", "black", "Open in browser: {$this->main->config->getAsString('rootUrl')}/{$app->manifest['rootUrlSlug']}/api/{$endpoint}");
    \Hubleto\Terminal::yellow("\n");
  }

}
