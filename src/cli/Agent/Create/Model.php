<?php

namespace HubletoMain\Cli\Agent\Create;

class Model extends \HubletoMain\Cli\Agent\Command
{

  public function run(): void
  {
    $appNamespace = (string) ($this->arguments[3] ?? '');
    $model = (string) ($this->arguments[4] ?? '');
    $force = (bool) ($this->arguments[5] ?? false);

    $this->main->apps->init();

    if (empty($appNamespace)) throw new \Exception("<appNamespace> not provided.");
    if (empty($model)) throw new \Exception("<model> not provided.");

    // $appManager = new \HubletoMain\Core\AppManager($this->main);
    $app = $this->main->apps->getAppInstance($appNamespace);

    if (!$app) throw new \Exception("App '{$appNamespace}' does not exist or is not installed.");

    $appFolder = $app->rootFolder;

    if (is_file($appFolder . '/Models/' . $model . '.php') && !$force) {
      throw new \Exception("Model '{$model}' already exists in app '{$appNamespace}'. Use 'force' to overwrite existing files.");
    }

    $tplFolder = __DIR__ . '/../../../code_templates/snippets';
    $this->main->addTwigViewNamespace($tplFolder, 'snippets');

    $tplVars = [
      'appNamespace' => $appNamespace,
      'model' => $model,
      'sqlTable' => strtolower($model),
    ];

    if (!is_dir($appFolder . '/Models')) mkdir($appFolder . '/Models');
    if (!is_dir($appFolder . '/Models/RecordManagers')) mkdir($appFolder . '/Models/RecordManagers');
    file_put_contents($appFolder . '/Models/' . $model . '.php', $this->main->twig->render('@snippets/Model.php.twig', $tplVars));
    file_put_contents($appFolder . '/Models/RecordManagers/' . $model . '.php', $this->main->twig->render('@snippets/ModelRecordManager.php.twig', $tplVars));

    $this->cli->cyan("Model '{$model}' in '{$appNamespace}' created successfully.\n");
    $this->cli->yellow("💡 TIP: Update `installTables()` method in your app's loader and reinstall the app.\n");
  }

}