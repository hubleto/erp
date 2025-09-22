<?php declare(strict_types=1);

namespace Hubleto\Erp\Cli\Agent\Create;

class Controller extends \Hubleto\Erp\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = $this->appManager()->sanitizeAppNamespace((string) ($this->arguments[3] ?? ''));
    $controller = (string) ($this->arguments[4] ?? '');
    $force = (bool) ($this->arguments[5] ?? false);

    $this->appManager()->init();

    if (empty($appNamespace)) {
      throw new \Exception("<appNamespace> not provided.");
    }
    if (empty($controller)) {
      throw new \Exception("<controller> not provided.");
    }

    $app = $this->appManager()->getApp($appNamespace);

    if (!$app) {
      throw new \Exception("App '{$appNamespace}' does not exist or is not installed.");
    }

    if (is_file($app->srcFolder . '/Controllers/' . $controller . '.php') && !$force) {
      throw new \Exception("Controller '{$controller}' already exists in app '{$appNamespace}'. Use 'force' to overwrite existing files.");
    }

    $tplFolder = __DIR__ . '/../../Templates/snippets';
    $this->renderer()->addNamespace($tplFolder, 'snippets');

    $tplVars = [
      'appNamespace' => $appNamespace,
      'appViewNamespace' => trim(str_replace('\\', ':', $appNamespace), ':'),
      'controller' => $controller,
    ];

    if (!is_dir($app->srcFolder . '/Controllers')) {
      mkdir($app->srcFolder . '/Controllers');
    }
    file_put_contents($app->srcFolder . '/Controllers/' . $controller . '.php', $this->renderer()->renderView('@snippets/Controller.php.twig', $tplVars));

    $this->terminal()->white("\n");
    $this->terminal()->cyan("Controller '{$controller}' in '{$appNamespace}' created successfully.\n");
    $this->terminal()->yellow("💡 TIP: Run 'php hubleto create view {$appNamespace} {$controller}' to create a view for this controler.\n");
  }

}
