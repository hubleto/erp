<?php declare(strict_types=1);

namespace Hubleto\Erp\Cli\Agent\Create;

class View extends \Hubleto\Erp\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = (string) ($this->arguments[3] ?? '');
    $view = (string) ($this->arguments[4] ?? '');
    $force = (bool) ($this->arguments[5] ?? false);

    $this->appManager()->init();

    if (empty($appNamespace)) {
      throw new \Exception("<appNamespace> not provided.");
    }
    if (empty($view)) {
      throw new \Exception("<view> not provided.");
    }

    $app = $this->appManager()->getApp($appNamespace);

    if (!$app) {
      throw new \Exception("App '{$appNamespace}' does not exist or is not installed.");
    }

    if (is_file($app->srcFolder . '/Views/' . $view . '.php') && !$force) {
      throw new \Exception("View '{$view}' already exists in app '{$appNamespace}'. Use 'force' to overwrite existing files.");
    }

    $tplFolder = __DIR__ . '/../../Templates/snippets';
    $this->renderer()->addNamespace($tplFolder, 'snippets');

    if (!is_dir($app->srcFolder . '/Views')) {
      mkdir($app->srcFolder . '/Viewss');
    }
    file_put_contents($app->srcFolder . '/Views/' . $view . '.twig', $this->renderer()->renderView('@snippets/View.twig.twig'));

    $this->terminal()->cyan("View '{$view}' in '{$appNamespace}' created successfully.\n");
    $this->terminal()->yellow("💡 TIP: Visit https://developer.hubleto.com/tutorial/add-route-controller-and-view on how to add routes for your controller and view.\n");
  }

}
