<?php

namespace HubletoMain\Cli\Agent\Create;

class TableFormViewAndController extends \HubletoMain\Cli\Agent\Command
{

  public function run(): void
  {
    $appNamespace = (string) ($this->arguments[3] ?? '');
    $model = (string) ($this->arguments[5] ?? '');
    $force = (bool) ($this->arguments[6] ?? false);

    $this->main->apps->init();

    if (empty($appNamespace)) throw new \Exception("<appNamespace> not provided.");
    if (empty($model)) throw new \Exception("<model> not provided.");

    $app = $this->main->apps->getAppInstance($appNamespace);

    if (!$app) throw new \Exception("App '{$appNamespace}' does not exist or is not installed.");

    $appFolder = $app->rootFolder;

    if (is_file($appFolder . '/Models/' . $model . '.php') && !$force) {
      throw new \Exception("Model '{$model}' already exists in app '{$appNamespace}'. Use 'force' to overwrite existing files.");
    }

    $tplFolder = __DIR__ . '/../../../code_templates/snippets';
    $this->main->addTwigViewNamespace($tplFolder, 'snippets');

    $controller = $model . 's'; // using plural for controller managing the records in the model
    $view = $model . 's'; // using plural for view managing the records in the model

    $tplVars = [
      'appNamespace' => $appNamespace,
      'model' => $model,
      'sqlTable' => strtolower($model),
      'controller' => $controller,
      'view' => $view,
    ];

    if (!is_dir($appFolder . '/Components')) mkdir($appFolder . '/Components');
    if (!is_dir($appFolder . '/Controllers')) mkdir($appFolder . '/Controllers');
    if (!is_dir($appFolder . '/Views')) mkdir($appFolder . '/Views');
    file_put_contents($appFolder . '/Components/Table' . $model . '.tsx', $this->main->twig->render('@snippets/ComponentTable.tsx.twig', $tplVars));
    file_put_contents($appFolder . '/Components/Form' . $model . '.tsx', $this->main->twig->render('@snippets/ComponentForm.tsx.twig', $tplVars));
    file_put_contents($appFolder . '/Controllers/' . $controller . '.php', $this->main->twig->render('@snippets/Controller.php.twig', $tplVars));
    file_put_contents($appFolder . '/Views/' . $view . '.php', $this->main->twig->render('@snippets/View.twig.twig', $tplVars));

    $this->cli->cyan("Table, form, view and controller for model '{$model}' in '{$appNamespace}' created successfully.\n");
    $this->cli->yellow("⚠ NEXT STEPS:\n");
    $this->cli->yellow("⚠  -> Add the Table component into Loader.tsx of your app.\n");
    $this->cli->yellow("⚠  -> Add the route in the `init()` method of your app's loader.\n");
    $this->cli->yellow("⚠   > Run `npm run build-js` in your terminal to compile new Typescript.\n");
  }

}