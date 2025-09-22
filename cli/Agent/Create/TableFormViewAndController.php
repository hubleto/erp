<?php declare(strict_types=1);

namespace Hubleto\Erp\Cli\Agent\Create;

class TableFormViewAndController extends \Hubleto\Erp\Cli\Agent\Command
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
    $controller = $modelPluralForm; // using plural for controller managing the records in the model
    $view = $modelPluralForm; // using plural for view managing the records in the model

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

    if (
      (
        is_file($app->srcFolder . '/Components/Table' . $modelPluralForm . '.tsx')
        || is_file($app->srcFolder . '/Components/Form' . $modelSingularForm . '.tsx')
        || is_file($app->srcFolder . '/Controllers/' . $controller . '.php')
        || is_file($app->srcFolder . '/Views/' . $view . '.php')
      )
      && !$force
    ) {
      throw new \Exception("Some of the MVC files for mode '{$model}' already exist in app '{$appNamespace}'. Use 'force' to overwrite existing files.");
    }

    $tplFolder = __DIR__ . '/../../Templates/snippets';
    $this->renderer()->addNamespace($tplFolder, 'snippets');

    $appNamespace = trim($appNamespace, '\\');
    $appNamespaceParts = explode('\\', $appNamespace);
    $appName = $appNamespaceParts[count($appNamespaceParts) - 1];
    $appNameKebab = \Hubleto\Framework\Helper::pascalToKebab($appName);

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

    if (!is_dir($app->srcFolder . '/Components')) {
      mkdir($app->srcFolder . '/Components');
    }
    if (!is_dir($app->srcFolder . '/Controllers')) {
      mkdir($app->srcFolder . '/Controllers');
    }
    if (!is_dir($app->srcFolder . '/Views')) {
      mkdir($app->srcFolder . '/Views');
    }
    file_put_contents($app->srcFolder . '/Components/Table' . $modelPluralForm . '.tsx', $this->renderer()->renderView('@snippets/Components/Table.tsx.twig', $tplVars));
    file_put_contents($app->srcFolder . '/Components/Form' . $modelSingularForm . '.tsx', $this->renderer()->renderView('@snippets/Components/Form.tsx.twig', $tplVars));
    file_put_contents($app->srcFolder . '/Controllers/' . $controller . '.php', $this->renderer()->renderView('@snippets/Controller.php.twig', $tplVars));
    file_put_contents($app->srcFolder . '/Views/' . $view . '.twig', $this->renderer()->renderView('@snippets/ViewWithTable.twig.twig', $tplVars));

    $codeLoaderTsxLine1 = [ "import Table{$modelPluralForm} from './Components/Table{$modelPluralForm}';" ];
    $codeLoaderTsxLine2 = [ "globalThis.main.registerReactComponent('{$appName}Table{$modelPluralForm}', Table{$modelPluralForm});" ];
    $codeRoute = [ "\$this->router()->get([ '/^{$app->manifest['rootUrlSlug']}\/" . strtolower($modelPluralFormKebab) . "(\/(?<recordId>\d+))?\/?$/' => Controllers\\{$controller}::class ]);" ];
    $codeButton = [
      "<a class='btn btn-large btn-square btn-transparent' href='{$app->manifest['rootUrlSlug']}/{$modelPluralFormKebab}'>",
      "  <span class='icon'><i class='fas fa-table'></i></span>",
      "  <span class='text'>{$modelPluralForm}</span>",
      "</a>",
    ];

    $codeLoaderTsxLine1Inserted = \Hubleto\Terminal::insertCodeToFile($app->srcFolder . '/Loader.tsx', '//@hubleto-cli:imports', $codeLoaderTsxLine1);
    $codeLoaderTsxLine2Inserted = \Hubleto\Terminal::insertCodeToFile($app->srcFolder . '/Loader.tsx', '//@hubleto-cli:register-components', $codeLoaderTsxLine2);
    $codeRouteInserted = \Hubleto\Terminal::insertCodeToFile($app->srcFolder . '/Loader.php', '//@hubleto-cli:routes', $codeRoute);
    $codeButtonInserted = \Hubleto\Terminal::insertCodeToFile($app->srcFolder . '/Views/Home.twig', '{# @hubleto-cli:buttons #}', $codeButton);

    \Hubleto\Terminal::white("\n");
    \Hubleto\Terminal::cyan("Table, form, view and controller for model '{$model}' in '{$appNamespace}' created successfully.\n");

    if (!$codeLoaderTsxLine1Inserted || !$codeLoaderTsxLine2Inserted) {
      \Hubleto\Terminal::yellow("⚠ Failed to add some code automatically\n");
      \Hubleto\Terminal::yellow("⚠  -> Add the Table component into {$app->srcFolder}/Loader.tsx\n");
      \Hubleto\Terminal::colored("cyan", "black", "Add to Loader.tsx:");
      \Hubleto\Terminal::colored("cyan", "black", join("\n", $codeLoaderTsxLine1));
      \Hubleto\Terminal::colored("cyan", "black", join("\n", $codeLoaderTsxLine2));
      \Hubleto\Terminal::yellow("\n");
    }

    if (!$codeRouteInserted) {
      \Hubleto\Terminal::yellow("⚠ Failed to add some code automatically\n");
      \Hubleto\Terminal::yellow("⚠  -> Add the route in the `init()` method of {$app->srcFolder}/Loader.php\n");
      \Hubleto\Terminal::colored("cyan", "black", "Add to Loader.php->init():");
      \Hubleto\Terminal::colored("cyan", "black", join("\n", $codeRoute));
      \Hubleto\Terminal::yellow("\n");
    }

    if (!$codeButtonInserted) {
      \Hubleto\Terminal::yellow("⚠ Failed to add some code automatically\n");
      \Hubleto\Terminal::yellow("⚠  -> Add button to any view in {$app->srcFolder}/Views, e.g. Home.twig\n");
      \Hubleto\Terminal::colored("cyan", "black", "Add to {$app->srcFolder}/Views/Home.twig:");
      \Hubleto\Terminal::colored("cyan", "black", join("\n", $codeButton));
      \Hubleto\Terminal::white("\n");
    }

    if ($noPrompt || \Hubleto\Terminal::confirm('Do you want to re-install the app?')) {
      $this->getService(\Hubleto\Erp\Cli\Agent\App\Install::class)
        ->setArguments($this->arguments)
        ->run()
      ;
    }

    \Hubleto\Terminal::yellow("⚠  NEXT STEPS:\n");
    \Hubleto\Terminal::yellow("⚠   -> Run `npm run build-js` in `{$this->env()->srcFolder}` to compile Javascript.\n");
    \Hubleto\Terminal::colored("cyan", "black", "Run: npm run build-js");
    \Hubleto\Terminal::colored("cyan", "black", "And then open in browser: {$this->env()->projectUrl}/{$app->manifest['rootUrlSlug']}/" . strtolower($modelPluralForm));
  }

}
