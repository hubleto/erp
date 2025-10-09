<?php declare(strict_types=1);

namespace Hubleto\Erp\Cli\Agent\Create;

class ApiEndpoint extends \Hubleto\Erp\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = $this->appManager()->sanitizeAppNamespace((string) ($this->arguments[3] ?? ''));
    $endpoint = (string) ($this->arguments[4] ?? '');
    $force = (bool) ($this->arguments[5] ?? false);

    $this->appManager()->init();

    if (empty($appNamespace)) {
      throw new \Exception("<appNamespace> not provided.");
    }
    if (empty($endpoint)) {
      throw new \Exception("<endpoint> not provided.");
    }

    $endpointPascalCase = \Hubleto\Framework\Helper::kebabToPascal($endpoint);

    $app = $this->appManager()->getApp($appNamespace);

    if (!$app) {
      throw new \Exception("App '{$appNamespace}' does not exist or is not installed.");
    }

    if (is_file($app->srcFolder . '/Controllers/Api/' . $endpointPascalCase . '.php') && !$force) {
      throw new \Exception("REST API endpoint '{$endpoint}' already exists in app '{$appNamespace}'. Use 'force' to overwrite existing files.");
    }

    $tplFolder = __DIR__ . '/../../Templates/snippets';
    $this->renderer()->addNamespace($tplFolder, 'snippets');

    $tplVars = [
      'appNamespace' => $appNamespace,
      'endpoint' => $endpoint,
      'endpointPascalCase' => $endpointPascalCase,
    ];

    if (!is_dir($app->srcFolder . '/Controllers')) {
      mkdir($app->srcFolder . '/Controllers');
    }
    if (!is_dir($app->srcFolder . '/Controllers/Api')) {
      mkdir($app->srcFolder . '/Controllers/Api');
    }
    file_put_contents($app->srcFolder . '/Controllers/Api/' . $endpointPascalCase . '.php', $this->renderer()->renderView('@snippets/ApiController.php.twig', $tplVars));

    $codeRoute = [ "\$this->router()->get([ '/^{$app->manifest['rootUrlSlug']}\/api\/{$endpoint}\/?$/' => Controllers\\Api\\{$endpointPascalCase}::class ]);" ];
    $codeRouteInserted = $this->terminal()->insertCodeToFile($app->srcFolder . '/Loader.php', '//@hubleto-cli:routes', $codeRoute);

    $this->terminal()->white("\n");
    $this->terminal()->cyan("REST API endpoint '{$endpoint}' in '{$appNamespace}' created successfully.\n");

    if (!$codeRouteInserted) {
      $this->terminal()->yellow("⚠ Failed to add some code automatically\n");
      $this->terminal()->yellow("⚠  -> Add the route in the `init()` method of {$app->srcFolder}/Loader.php\n");
      $this->terminal()->colored("cyan", "black", "Add to Loader.php->init():\n");
      $this->terminal()->colored("cyan", "black", join("\n", $codeRoute) . "\n");
      $this->terminal()->yellow("\n");
    }

    $this->terminal()->yellow("💡  TIPS:\n");
    $this->terminal()->yellow("💡  -> Test the endpoint\n");
    $this->terminal()->colored("cyan", "black", "Open in browser: {$this->env()->projectUrl}/{$app->manifest['rootUrlSlug']}/api/{$endpoint}\n");
    $this->terminal()->yellow("\n");
  }

}
