<?php

namespace HubletoMain\Cli\Agent\Create;

class View extends \HubletoMain\Cli\Agent\Command
{

  public function run(): void
  {
    $appNamespace = (string) ($this->arguments[3] ?? '');
    $view = (string) ($this->arguments[4] ?? '');
    $force = (bool) ($this->arguments[5] ?? false);

    $this->main->apps->init();

    if (empty($appNamespace)) throw new \Exception("<appNamespace> not provided.");
    if (empty($view)) throw new \Exception("<view> not provided.");

    $app = $this->main->apps->getAppInstance($appNamespace);

    if (!$app) throw new \Exception("App '{$appNamespace}' does not exist or is not installed.");

    $rootFolder = $app->rootFolder;

    if (is_file($rootFolder . '/Views/' . $view . '.php') && !$force) {
      throw new \Exception("View '{$view}' already exists in app '{$appNamespace}'. Use 'force' to overwrite existing files.");
    }

    $tplFolder = __DIR__ . '/../../../code_templates/snippets';
    $this->main->addTwigViewNamespace($tplFolder, 'snippets');

    if (!is_dir($rootFolder . '/Views')) mkdir($rootFolder . '/Viewss');
    file_put_contents($rootFolder . '/Views/' . $view . '.twig', $this->main->twig->render('@snippets/View.twig.twig'));

    $this->cli->cyan("View '{$view}' in '{$appNamespace}' created successfully.\n");
    $this->cli->yellow("💡 TIP: Visit https://developer.hubleto.com/tutorial/add-route-controller-and-view on how to add routes for your controller and view.\n");
  }

}