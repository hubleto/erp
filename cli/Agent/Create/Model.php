<?php declare(strict_types=1);

namespace HubletoMain\Cli\Agent\Create;

class Model extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = $this->main->apps->sanitizeAppNamespace((string) ($this->arguments[3] ?? ''));
    $model = (string) ($this->arguments[4] ?? '');
    $force = (bool) ($this->arguments[5] ?? false);

    $modelSingularForm = $model;
    $modelPluralForm = $model . 's';
    $modelPluralFormKebab = \Hubleto\Framework\Helper::pascalToKebab($modelPluralForm);

    $this->main->apps->init();

    if (empty($appNamespace)) {
      throw new \Exception("<appNamespace> not provided.");
    }
    if (empty($model)) {
      throw new \Exception("<model> not provided.");
    }

    $app = $this->main->apps->getAppInstance($appNamespace);

    if (!$app) {
      throw new \Exception("App '{$appNamespace}' does not exist or is not installed.");
    }

    if (is_file($app->srcFolder . '/Models/' . $model . '.php') && !$force) {
      throw new \Exception("Model '{$model}' already exists in app '{$appNamespace}'. Use 'force' to overwrite existing files.");
    }

    $tplFolder = __DIR__ . '/../../Templates/snippets';
    $this->main->addTwigViewNamespace($tplFolder, 'snippets');

    $tplVars = [
      'appNamespace' => $appNamespace,
      'model' => $model,
      'sqlTable' => strtolower($modelPluralForm),
      'modelPluralFormKebab' => $modelPluralFormKebab,
    ];

    if (!is_dir($app->srcFolder . '/Models')) {
      mkdir($app->srcFolder . '/Models');
    }
    if (!is_dir($app->srcFolder . '/Models/RecordManagers')) {
      mkdir($app->srcFolder . '/Models/RecordManagers');
    }
    file_put_contents($app->srcFolder . '/Models/' . $model . '.php', $this->main->twig->render('@snippets/Model.php.twig', $tplVars));
    file_put_contents($app->srcFolder . '/Models/RecordManagers/' . $model . '.php', $this->main->twig->render('@snippets/ModelRecordManager.php.twig', $tplVars));

    $codeInstallModel = [
      "\$this->main->di->create(Models\\{$model}::class)->dropTableIfExists()->install();"
    ];

    $codeInstallModelInserted = \Hubleto\Terminal::insertCodeToFile(
      $app->srcFolder . '/Loader.php',
      '//@hubleto-cli:install-tables',
      $codeInstallModel
    );

    \Hubleto\Terminal::white("\n");
    \Hubleto\Terminal::cyan("Model '{$model}' in '{$appNamespace}' with sample set of columns created successfully.\n");

    if (!$codeInstallModelInserted) {
      \Hubleto\Terminal::yellow("⚠ Failed to add some code automatically\n");
      \Hubleto\Terminal::yellow("⚠  -> Add the model in `installTables()` method in  {$app->srcFolder}/Loader.php\n");
      \Hubleto\Terminal::colored("cyan", "black", "Add to Loader.php->installTables():");
      \Hubleto\Terminal::colored("cyan", "black", join("\n", $codeInstallModel));
      \Hubleto\Terminal::white("\n");
    }

    if (\Hubleto\Terminal::confirm('Do you want to re-install the app with your new model now?')) {
      (new \HubletoMain\Cli\Agent\App\Install($this->main, $this->arguments))->run();
    }

    \Hubleto\Terminal::yellow("💡  TIPS:\n");
    \Hubleto\Terminal::yellow("💡  -> Add columns to the model in model's `describeColumns()` method.\n");
    \Hubleto\Terminal::yellow("💡  -> Run command below to add controllers, views and some UI components to manage data in your model.\n");
    \Hubleto\Terminal::colored("cyan", "black", "Run: php hubleto create mvc {$appNamespace} {$model}");
  }

}
