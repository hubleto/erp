<?php declare(strict_types=1);

namespace Hubleto\Erp\Cli\Agent\Create;

class Model extends \Hubleto\Erp\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = $this->appManager()->sanitizeAppNamespace((string) ($this->arguments[3] ?? ''));
    $model = (string) ($this->arguments[4] ?? '');
    $force = (bool) ($this->arguments[5] ?? false);
    $noPrompt = (bool) ($this->arguments[6] ?? false);

    $modelSingularForm = $model;
    $modelPluralForm = $model . 's';
    $modelPluralFormKebab = \Hubleto\Framework\Helper::pascalToKebab($modelPluralForm);

    $this->appManager()->init();

    if (empty($appNamespace)) {
      throw new \Exception("<appNamespace> not provided.");
    }
    if (empty($model)) {
      throw new \Exception("<model> not provided.");
    }

    $app = $this->appManager()->getApp($appNamespace);

    if (!$app) {
      throw new \Exception("App '{$appNamespace}' does not exist or is not installed.");
    }

    if (is_file($app->srcFolder . '/Models/' . $model . '.php') && !$force) {
      throw new \Exception("Model '{$model}' already exists in app '{$appNamespace}'. Use 'force' to overwrite existing files.");
    }

    $tplFolder = __DIR__ . '/../../Templates/snippets';
    $this->renderer()->addNamespace($tplFolder, 'snippets');

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
    file_put_contents($app->srcFolder . '/Models/' . $model . '.php', $this->renderer()->renderView('@snippets/Model.php.twig', $tplVars));
    file_put_contents($app->srcFolder . '/Models/RecordManagers/' . $model . '.php', $this->renderer()->renderView('@snippets/ModelRecordManager.php.twig', $tplVars));

    $codeInstallModel = [
      "\$this->getModel(Models\\{$model}::class)->dropTableIfExists()->install();"
    ];

    $codeInstallModelInserted = $this->terminal()->insertCodeToFile(
      $app->srcFolder . '/Loader.php',
      '//@hubleto-cli:install-tables',
      $codeInstallModel
    );

    $this->terminal()->white("\n");
    $this->terminal()->cyan("Model '{$model}' in '{$appNamespace}' with sample set of columns created successfully.\n");

    if (!$codeInstallModelInserted) {
      $this->terminal()->yellow("⚠ Failed to add some code automatically\n");
      $this->terminal()->yellow("⚠  -> Add the model in `installTables()` method in  {$app->srcFolder}/Loader.php\n");
      $this->terminal()->colored("cyan", "black", "Add to Loader.php->installTables():\n");
      $this->terminal()->colored("cyan", "black", join("\n", $codeInstallModel) . "\n");
      $this->terminal()->white("\n");
    }

    if ($noPrompt || $this->terminal()->confirm('Do you want to re-install the app with your new model now?')) {
      $this->getService(\Hubleto\Erp\Cli\Agent\App\Install::class)
        ->setTerminalOutput($this->terminal()->output)
        ->setArguments($this->arguments)
        ->run()
      ;
    }

    $this->terminal()->yellow("💡  TIPS:\n");
    $this->terminal()->yellow("💡  -> Add columns to the model in model's `describeColumns()` method.\n");
    $this->terminal()->yellow("💡  -> Run command below to add controllers, views and some UI components to manage data in your model.\n");
    $this->terminal()->colored("cyan", "black", "Run: php hubleto create mvc {$appNamespace} {$model}\n");
  }

}
