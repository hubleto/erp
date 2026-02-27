<?php declare(strict_types=1);

namespace Hubleto\Erp\Cli\Agent\Create;

use Hubleto\Framework\Interfaces\ModelInterface;
use Hubleto\Framework\Db\ModelSQLCommandsGenerator;

class Migration extends \Hubleto\Erp\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = $this->appManager()->sanitizeAppNamespace((string) ($this->arguments[3] ?? ''));
    $model = (string) ($this->arguments[4] ?? '');
    $noPrompt = (bool) ($this->arguments[5] ?? false);

    $modelSingularForm = $model;
    $modelPluralForm = $model . 's';
    $modelPluralFormKebab = \Hubleto\Framework\Helper::pascalToKebab($modelPluralForm);

    $this->appManager()->init();

    if (empty($appNamespace)) {
      throw new \Exception("<appNamespace> not provided.");
    }
    if (empty($model)) {
      if (!$noPrompt) {
        $allModels = $this->terminal()->confirm("Model name not provided. Do you want to do this for all models in the specified app?");
        if (!$allModels) {
          throw new \Exception("<model> not provided.");
        }
      }
    }

    $app = $this->appManager()->getApp($appNamespace);

    if (!$app) {
      throw new \Exception("App '{$appNamespace}' does not exist or is not installed.");
    }

    $tplFolder = __DIR__ . '/../../Templates/snippets';
    $this->renderer()->addNamespace($tplFolder, 'snippets');

    if (empty($model)) {
      $queue = $app->getAvailableModelClasses();
    } else {
      $queue = [ $appNamespace . '\\Models\\' . $model];
    }

    /** @var ModelSQLCommandsGenerator */
    $sqlGenerator = $this->getService(ModelSQLCommandsGenerator::class);

    foreach ($queue as $class) {
      $classObject = new $class;

      if (!($classObject instanceof ModelInterface)) {
        throw new \Exception("Class '{$class}' does not implement ModelInterface.");
      }

      $className = basename(str_replace('\\', '/', $class));

      if (!is_file($app->srcFolder . '/Models/' . $className . '.php')) {
        throw new \Exception("Model '{$class}' does not exist in app '{$appNamespace}'.");
      }

      $createTableCommands = array_filter($sqlGenerator->getSqlCreateTableCommands($classObject));
      $createIndexCommands = array_filter($sqlGenerator->getSqlCreateIndexesCommands($classObject));
      $createFkCommands = array_filter($sqlGenerator->getSqlCreateForeignKeysCommands($classObject));
      $dropFkCommands = array_filter($sqlGenerator->getSqlDropForeignKeysCommands($classObject));

      $dropTableIfExists = join(";\n", $sqlGenerator->getSqlDropTableIfExists($classObject)) . ';';
      $installTables = join(";\n", $createTableCommands) . ';';
      if (!empty($createIndexCommands)) {
        $installTables .= "\n\n" . join("; ", $createIndexCommands) . ';';
      }

      $tplVars = [
        'appNamespace' => $appNamespace,
        'model' => $className,
        'date' => date('Ymd'),
        'dropTableIfExists' => !empty($dropTableIfExists) ? '$this->db->execute("' . $dropTableIfExists . '");' : '',
        'installTables' => !empty($installTables) ? '$this->db->execute("' . $installTables . '");' : '',
        'installForeignKeys' => !empty(join("; ", $createFkCommands)) ? '$this->db->execute("' . join("; ", $createFkCommands) . ';'. '");' : '',
        'uninstallForeignKeys' => !empty(join("; ", $dropFkCommands)) ? '$this->db->execute("' .join("; ", $dropFkCommands) . ';'. '");' : '',
      ];

      if (!is_dir($app->srcFolder . '/Models')) {
        mkdir($app->srcFolder . '/Models');
      }
      if (!is_dir($app->srcFolder . '/Models/Migrations')) {
        mkdir($app->srcFolder . '/Models/Migrations');
      }
      file_put_contents($app->srcFolder . '/Models/Migrations/' . $className . '_' . date('Ymd') . '_0001.php', $this->renderer()->renderView('@snippets/Migration.php.twig', $tplVars));

      $this->terminal()->white("\n");
      $this->terminal()->cyan("Migration " . $class . '_' . date('Ymd') . '_0001.php' . " in '{$appNamespace}' created successfully.\n");
    }

    $this->terminal()->yellow("💡  TIPS:\n");
    $this->terminal()->yellow("💡  -> Make sure to verify whether the generated migrations are correct.\n");
  }

}
