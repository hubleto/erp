<?php

namespace HubletoMain\Cli\Agent\Create;

class Model extends \HubletoMain\Cli\Agent\Command
{

  public function run(): void
  {
    $appNamespace = (string) ($this->arguments[3] ?? '');
    $model = (string) ($this->arguments[4] ?? '');
    $force = (bool) ($this->arguments[5] ?? false);

    $modelSingularForm = $model;
    $modelPluralForm = $model . 's';

    $this->main->apps->init();

    if (empty($appNamespace)) throw new \Exception("<appNamespace> not provided.");
    if (empty($model)) throw new \Exception("<model> not provided.");

    $app = $this->main->apps->getAppInstance($appNamespace);

    if (!$app) throw new \Exception("App '{$appNamespace}' does not exist or is not installed.");

    $rootFolder = $app->rootFolder;

    if (is_file($rootFolder . '/Models/' . $model . '.php') && !$force) {
      throw new \Exception("Model '{$model}' already exists in app '{$appNamespace}'. Use 'force' to overwrite existing files.");
    }

    $tplFolder = __DIR__ . '/../../../code_templates/snippets';
    $this->main->addTwigViewNamespace($tplFolder, 'snippets');

    $tplVars = [
      'appNamespace' => $appNamespace,
      'model' => $model,
      'sqlTable' => strtolower($modelPluralForm),
    ];

    if (!is_dir($rootFolder . '/Models')) mkdir($rootFolder . '/Models');
    if (!is_dir($rootFolder . '/Models/RecordManagers')) mkdir($rootFolder . '/Models/RecordManagers');
    file_put_contents($rootFolder . '/Models/' . $model . '.php', $this->main->twig->render('@snippets/Model.php.twig', $tplVars));
    file_put_contents($rootFolder . '/Models/RecordManagers/' . $model . '.php', $this->main->twig->render('@snippets/ModelRecordManager.php.twig', $tplVars));

    $this->cli->white("\n");
    $this->cli->cyan("Model '{$model}' in '{$appNamespace}' with sample set of columns created successfully.\n");
    $this->cli->yellow("⚠ NEXT STEPS:\n");
    $this->cli->yellow("⚠  -> Modify `describeColumns()` method in the model.\n");
    $this->cli->yellow("⚠  -> Add the model in `installTables()` method in  {$app->rootFolder}/Loader.php\n");
    $this->cli->blue("(new Models\\{$model}(\$this->main))->dropTableIfExists()->install();\n");
    $this->cli->white("\n");
    $this->cli->yellow("⚠  -> Re-install the app.\n");
    $this->cli->blue("php hubleto app install {$appNamespace} force\n");
    $this->cli->white("\n");
    $this->cli->yellow("💡 TIP: Run command below to add controllers, views and some UI components to manage data in your model.\n");
    $this->cli->blue("php hubleto create mvc {$appNamespace} Order\n");
  }

}