<?php

namespace HubletoMain\Cli\Agent\Create;

class TableFormViewAndController extends \HubletoMain\Cli\Agent\Command
{

  public function run(): void
  {

    // now create view and controller
    $appNamespace = (string) ($this->arguments[3] ?? '');
    $model = (string) ($this->arguments[4] ?? '');
    $force = (bool) ($this->arguments[5] ?? false);

    $modelSingularForm = $model;
    $modelPluralForm = $model . 's';
    $controller = $modelPluralForm; // using plural for controller managing the records in the model
    $view = $modelPluralForm; // using plural for view managing the records in the model

    $this->main->apps->init();

    if (empty($appNamespace)) throw new \Exception("<appNamespace> not provided.");
    if (empty($model)) throw new \Exception("<model> not provided.");

    $app = $this->main->apps->getAppInstance($appNamespace);

    if (!$app) throw new \Exception("App '{$appNamespace}' does not exist or is not installed.");

    $rootFolder = $app->rootFolder;

    if (
      (
        is_file($rootFolder . '/Components/Table' . $modelPluralForm . '.tsx')
        || is_file($rootFolder . '/Components/Form' . $modelSingularForm . '.tsx')
        || is_file($rootFolder . '/Controllers/' . $controller . '.php')
        || is_file($rootFolder . '/Views/' . $view . '.php')
      )
      && !$force
    ) {
      throw new \Exception("Some of the MVC files for mode '{$model}' already exist in app '{$appNamespace}'. Use 'force' to overwrite existing files.");
    }

    $tplFolder = __DIR__ . '/../../../code_templates/snippets';
    $this->main->addTwigViewNamespace($tplFolder, 'snippets');

    $appNamespace = trim($appNamespace, '\\');
    $appNamespaceParts = explode('\\', $appNamespace);
    $appName = $appNamespaceParts[count($appNamespaceParts) - 1];

    $appNamespaceForwardSlash = str_replace('\\', '/', $appNamespace);
    $appNamespaceDoubleBackslash = str_replace('/', '\\\\', $appNamespaceForwardSlash);

    $tplVars = [
      'appNamespace' => $appNamespace,
      'appNamespaceForwardSlash' => $appNamespaceForwardSlash,
      'appNamespaceDoubleBackslash' => $appNamespaceDoubleBackslash,
      'appName' => $appName,
      'model' => $model,
      'modelSingularForm' => $modelSingularForm,
      'modelPluralForm' => $modelPluralForm,
      'sqlTable' => strtolower($model),
      'controller' => $controller,
      'appViewNamespace' => trim(str_replace('\\', ':', $appNamespace), ':'),
      'view' => $view,
    ];

    if (!is_dir($rootFolder . '/Components')) mkdir($rootFolder . '/Components');
    if (!is_dir($rootFolder . '/Controllers')) mkdir($rootFolder . '/Controllers');
    if (!is_dir($rootFolder . '/Views')) mkdir($rootFolder . '/Views');
    file_put_contents($rootFolder . '/Components/Table' . $modelPluralForm . '.tsx', $this->main->twig->render('@snippets/Components/Table.tsx.twig', $tplVars));
    file_put_contents($rootFolder . '/Components/Form' . $modelSingularForm . '.tsx', $this->main->twig->render('@snippets/Components/Form.tsx.twig', $tplVars));
    file_put_contents($rootFolder . '/Controllers/' . $controller . '.php', $this->main->twig->render('@snippets/Controller.php.twig', $tplVars));
    file_put_contents($rootFolder . '/Views/' . $view . '.twig', $this->main->twig->render('@snippets/ViewWithTable.twig.twig', $tplVars));

    $this->cli->white("\n");
    $this->cli->cyan("Table, form, view and controller for model '{$model}' in '{$appNamespace}' created successfully.\n");
    $this->cli->yellow("⚠  NEXT STEPS:\n");
    $this->cli->yellow("⚠  -> Add the Table component into {$app->rootFolder}/Loader.tsx\n");
    $this->cli->colored("cyan", "black", "Add to Loader.tsx:");
    $this->cli->colored("cyan", "black", "import Table{$modelPluralForm} from './Components/Table{$modelPluralForm}';");
    $this->cli->colored("cyan", "black", "globalThis.main.registerReactComponent('{$appName}Table{$modelPluralForm}', Table{$modelPluralForm});");
    $this->cli->yellow("\n");
    $this->cli->yellow("⚠  -> Add the route in the `init()` method of {$app->rootFolder}/Loader.php\n");
    $this->cli->colored("cyan", "black", "Add to Loader.php->init(): \$this->main->router->httpGet([ '/^{$app->manifest['rootUrlSlug']}\/" . strtolower($modelPluralForm) . "\/?$/' => Controllers\\{$controller}::class ]);");
    $this->cli->yellow("\n");
    $this->cli->yellow("⚠  -> Add button to any view in {$app->rootFolder}/Views, e.g. Home.twig\n");
    $this->cli->colored("cyan", "black", "Add to {$app->rootFolder}/Views/Home.twig:");
    $this->cli->colored("cyan", "black", "<a class='btn btn-large btn-square btn-transparent'>");
    $this->cli->colored("cyan", "black", "  <span class='icon'><i class='fas fa-table'></i></span>");
    $this->cli->colored("cyan", "black", "  <span class='text'}{$modelPluralForm}</span>");
    $this->cli->colored("cyan", "black", "</a>");
    $this->cli->white("\n");
    $this->cli->yellow("⚠  -> Re-install the app.\n");
    $this->cli->colored("cyan", "black", "Run: php hubleto app install {$appNamespace} force");
    $this->cli->yellow("\n");
    $this->cli->yellow("⚠   -> Run `npm i` in `{$this->main->config->getAsString('srcFolder')}` to install required node modules.\n");
    $this->cli->yellow("⚠   -> Run `npm run build-js` or `npm run watch-js` in `{$this->main->config->getAsString('srcFolder')}` to compile Javascript.\n");
    $this->cli->colored("cyan", "black", "Run: npm run --prefix hbl build-js");
    $this->cli->colored("cyan", "black", "And then open in browser: {$this->main->config->getAsString('rootUrl')}/{$app->manifest['rootUrlSlug']}/" . strtolower($modelPluralForm));
  }

}