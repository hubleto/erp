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
    $modelPluralFormKebab = \ADIOS\Core\Helper::pascalToKebab($modelPluralForm);
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
    $appNameKebab = \ADIOS\Core\Helper::pascalToKebab($appName);

    $appNamespaceForwardSlash = str_replace('\\', '/', $appNamespace);
    $appNamespaceDoubleBackslash = str_replace('/', '\\\\', $appNamespaceForwardSlash);

    $tplVars = [
      'appNamespace' => $appNamespace,
      'appNamespaceForwardSlash' => $appNamespaceForwardSlash,
      'appNamespaceDoubleBackslash' => $appNamespaceDoubleBackslash,
      'appName' => $appName,
      'appNameKebab' => $appNameKebab,
      'model' => $model,
      'modelSingularForm' => $modelSingularForm,
      'modelPluralForm' => $modelPluralForm,
      'modelPluralFormKebab' => $modelPluralFormKebab,
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

    $codeLoaderTsxLine1 = [ "import Table{$modelPluralForm} from './Components/Table{$modelPluralForm}';" ];
    $codeLoaderTsxLine2 = [ "globalThis.main.registerReactComponent('{$appName}Table{$modelPluralForm}', Table{$modelPluralForm});" ];
    $codeRoute = [ "\$this->main->router->httpGet([ '/^{$app->manifest['rootUrlSlug']}\/" . strtolower($modelPluralFormKebab) . "(\/(?<recordId>\d+))?\/?$/' => Controllers\\{$controller}::class ]);" ];
    $codeButton = [
      "<a class='btn btn-large btn-square btn-transparent' href='{$app->manifest['rootUrlSlug']}/{$modelPluralFormKebab}'>",
      "  <span class='icon'><i class='fas fa-table'></i></span>",
      "  <span class='text'>{$modelPluralForm}</span>",
      "</a>",
    ];

    $codeLoaderTsxLine1Inserted = $this->cli->insertCodeToFile($rootFolder . '/Loader.tsx', '//@hubleto-cli:imports', $codeLoaderTsxLine1);
    $codeLoaderTsxLine2Inserted = $this->cli->insertCodeToFile($rootFolder . '/Loader.tsx', '//@hubleto-cli:register-components', $codeLoaderTsxLine2);
    $codeRouteInserted = $this->cli->insertCodeToFile($rootFolder . '/Loader.php', '//@hubleto-cli:routes', $codeRoute);
    $codeButtonInserted = $this->cli->insertCodeToFile($rootFolder . '/Views/Home.twig', '{# @hubleto-cli:buttons #}', $codeButton);

    $this->cli->white("\n");
    $this->cli->cyan("Table, form, view and controller for model '{$model}' in '{$appNamespace}' created successfully.\n");

    if (!$codeLoaderTsxLine1Inserted || !$codeLoaderTsxLine2Inserted) {
      $this->cli->yellow("⚠ Failed to add some code automatically\n");
      $this->cli->yellow("⚠  -> Add the Table component into {$app->rootFolder}/Loader.tsx\n");
      $this->cli->colored("cyan", "black", "Add to Loader.tsx:");
      $this->cli->colored("cyan", "black", join("\n", $codeLoaderTsxLine1));
      $this->cli->colored("cyan", "black", join("\n", $codeLoaderTsxLine2));
      $this->cli->yellow("\n");
    }

    if (!$codeRouteInserted) {
      $this->cli->yellow("⚠ Failed to add some code automatically\n");
      $this->cli->yellow("⚠  -> Add the route in the `init()` method of {$app->rootFolder}/Loader.php\n");
      $this->cli->colored("cyan", "black", "Add to Loader.php->init():");
      $this->cli->colored("cyan", "black", join("\n", $codeRoute));
      $this->cli->yellow("\n");
    }

    if (!$codeButtonInserted) {
      $this->cli->yellow("⚠ Failed to add some code automatically\n");
      $this->cli->yellow("⚠  -> Add button to any view in {$app->rootFolder}/Views, e.g. Home.twig\n");
      $this->cli->colored("cyan", "black", "Add to {$app->rootFolder}/Views/Home.twig:");
      $this->cli->colored("cyan", "black", join("\n", $codeButton));
      $this->cli->white("\n");
    }

    if ($this->cli->confirm('Do you want to re-install the app?')) {
      (new \HubletoMain\Cli\Agent\App\Install($this->cli, $this->arguments))->run();
    }

    $this->cli->yellow("⚠  NEXT STEPS:\n");
    $this->cli->yellow("⚠   -> Run `npm run build-js` in `{$this->main->config->getAsString('srcFolder')}` to compile Javascript.\n");
    $this->cli->colored("cyan", "black", "Run: npm run --prefix hbl build-js");
    $this->cli->colored("cyan", "black", "And then open in browser: {$this->main->config->getAsString('rootUrl')}/{$app->manifest['rootUrlSlug']}/" . strtolower($modelPluralForm));
  }

}