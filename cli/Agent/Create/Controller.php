<?php declare(strict_types=1);

namespace Hubleto\Erp\Cli\Agent\Create;

class Controller extends \Hubleto\Erp\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = $this->getAppManager()->sanitizeAppNamespace((string) ($this->arguments[3] ?? ''));
    $controller = (string) ($this->arguments[4] ?? '');
    $force = (bool) ($this->arguments[5] ?? false);

    $this->getAppManager()->init();

    if (empty($appNamespace)) {
      throw new \Exception("<appNamespace> not provided.");
    }
    if (empty($controller)) {
      throw new \Exception("<controller> not provided.");
    }

    $app = $this->getAppManager()->getApp($appNamespace);

    if (!$app) {
      throw new \Exception("App '{$appNamespace}' does not exist or is not installed.");
    }

    if (is_file($app->srcFolder . '/Controllers/' . $controller . '.php') && !$force) {
      throw new \Exception("Controller '{$controller}' already exists in app '{$appNamespace}'. Use 'force' to overwrite existing files.");
    }

    $tplFolder = __DIR__ . '/../../Templates/snippets';
    $this->getRenderer()->addNamespace($tplFolder, 'snippets');

    $tplVars = [
      'appNamespace' => $appNamespace,
      'appViewNamespace' => trim(str_replace('\\', ':', $appNamespace), ':'),
      'controller' => $controller,
    ];

    if (!is_dir($app->srcFolder . '/Controllers')) {
      mkdir($app->srcFolder . '/Controllers');
    }
    file_put_contents($app->srcFolder . '/Controllers/' . $controller . '.php', $this->getRenderer()->renderView('@snippets/Controller.php.twig', $tplVars));

    \Hubleto\Terminal::white("\n");
    \Hubleto\Terminal::cyan("Controller '{$controller}' in '{$appNamespace}' created successfully.\n");
    \Hubleto\Terminal::yellow("💡 TIP: Run 'php hubleto create view {$appNamespace} {$controller}' to create a view for this controler.\n");
  }

}
