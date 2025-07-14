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
    $modelPluralFormKebab = \ADIOS\Core\Helper::pascalToKebab($modelPluralForm);

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
      'modelPluralFormKebab' => $modelPluralFormKebab,
    ];

    if (!is_dir($rootFolder . '/Models')) mkdir($rootFolder . '/Models');
    if (!is_dir($rootFolder . '/Models/RecordManagers')) mkdir($rootFolder . '/Models/RecordManagers');
    file_put_contents($rootFolder . '/Models/' . $model . '.php', $this->main->twig->render('@snippets/Model.php.twig', $tplVars));
    file_put_contents($rootFolder . '/Models/RecordManagers/' . $model . '.php', $this->main->twig->render('@snippets/ModelRecordManager.php.twig', $tplVars));

    $codeInstallModel = [
      "(new Models\\{$model}(\$this->main))->dropTableIfExists()->install();"
    ];

    $codeInstallModelInserted = $this->cli->insertCodeToFile(
      $rootFolder . '/Loader.php',
      '//@hubleto-cli:install-tables',
      $codeInstallModel
    );

    $this->cli->white("\n");
    $this->cli->cyan("Model '{$model}' in '{$appNamespace}' with sample set of columns created successfully.\n");

    if (!$codeInstallModelInserted) {
      $this->cli->yellow("⚠ Failed to add some code automatically\n");
      $this->cli->yellow("⚠  -> Add the model in `installTables()` method in  {$app->rootFolder}/Loader.php\n");
      $this->cli->colored("cyan", "black", "Add to Loader.php->installTables():");
      $this->cli->colored("cyan", "black", join("\n", $codeInstallModel));
      $this->cli->white("\n");
    }

    if ($this->cli->confirm('Do you want to re-install the app with your new model now?')) {
      (new \HubletoMain\Cli\Agent\App\Install($this->cli, $this->arguments))->run();
    }

    $this->cli->yellow("💡  TIPS:\n");
    $this->cli->yellow("💡  -> Add columns to the model in model's `describeColumns()` method.\n");
    $this->cli->yellow("💡  -> Run command below to add controllers, views and some UI components to manage data in your model.\n");
    $this->cli->colored("cyan", "black", "Run: php hubleto create mvc {$appNamespace} {$model}");
  }

}