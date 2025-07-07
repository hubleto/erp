<?php

namespace HubletoMain\Cli\Agent\Create;

class Controller extends \HubletoMain\Cli\Agent\Command
{

  public function run(): void
  {
    $appNamespace = (string) ($this->arguments[3] ?? '');
    $controller = (string) ($this->arguments[4] ?? '');
    $force = (bool) ($this->arguments[5] ?? false);

    $this->main->apps->init();

    if (empty($appNamespace)) throw new \Exception("<appNamespace> not provided.");
    if (empty($controller)) throw new \Exception("<controller> not provided.");

    $app = $this->main->apps->getAppInstance($appNamespace);

    if (!$app) throw new \Exception("App '{$appNamespace}' does not exist or is not installed.");

    $rootFolder = $app->rootFolder;

    if (is_file($rootFolder . '/Controllers/' . $controller . '.php') && !$force) {
      throw new \Exception("Controller '{$controller}' already exists in app '{$appNamespace}'. Use 'force' to overwrite existing files.");
    }

    $tplFolder = __DIR__ . '/../../../code_templates/snippets';
    $this->main->addTwigViewNamespace($tplFolder, 'snippets');

    $tplVars = [
      'appNamespace' => $appNamespace,
      'appViewNamespace' => trim(str_replace('\\', ':', $appNamespace), ':'),
      'controller' => $controller,
    ];

    if (!is_dir($rootFolder . '/Controllers')) mkdir($rootFolder . '/Controllers');
    file_put_contents($rootFolder . '/Controllers/' . $controller . '.php', $this->main->twig->render('@snippets/Controller.php.twig', $tplVars));

    $this->cli->white("\n");
    $this->cli->cyan("Controller '{$controller}' in '{$appNamespace}' created successfully.\n");
    $this->cli->yellow("💡 TIP: Run 'php hubleto create view {$appNamespace} {$controller}' to create a view for this controler.\n");
  }

}