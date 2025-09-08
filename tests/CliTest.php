<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class CliTest extends TestCase
{

  /**
   * [Description for args]
   *
   * @return array
   * 
   */
  public function args(): array
  {
    $args = array_merge(
      [
        '', // executable path
        '', // executable path
        '', // command to be executed
      ],
      func_get_args()
    );

    return $args;
  }

  /**
   * [Description for testCreateApp]
   *
   * @return void
   * 
   */
  public function testCreateApp(): void
  {
    $main = \Hubleto\Erp\Loader::getGlobalApp();
    (new \Hubleto\Erp\Cli\Agent\App\Create($main, $this->args(
      'Hubleto\\App\\Custom\\TestApp', // appNamespace
      true // noPrompt
    )))->run();

    $this->assertDirectoryExists($main->projectFolder . '/src/apps/TestApp/Controllers');
    $this->assertDirectoryExists($main->projectFolder . '/src/apps/TestApp/Views');
    $this->assertFileExists($main->projectFolder . '/src/apps/TestApp/manifest.yaml');
    $this->assertFileExists($main->projectFolder . '/src/apps/TestApp/Loader.php');
    $this->assertFileExists($main->projectFolder . '/src/apps/TestApp/Loader.tsx');
  }

  /**
   * [Description for testCreateModel]
   *
   * @return void
   * 
   */
  public function testCreateModel(): void
  {
    $main = \Hubleto\Erp\Loader::getGlobalApp();
    (new \Hubleto\Erp\Cli\Agent\Create\Model($main, $this->args(
      'Hubleto\\App\\Custom\\TestApp', // appNamespace
      'TestModel', // model
      true, // force
      true // noPrompt
    )))->run();

    $this->assertFileExists($main->projectFolder . '/src/apps/TestApp/Models/TestModel.php');
    $this->assertFileExists($main->projectFolder . '/src/apps/TestApp/Models/RecordManagers/TestModel.php');
  }

  /**
   * [Description for testCreateMvcForModel]
   *
   * @return void
   * 
   */
  public function testCreateMvcForModel(): void
  {
    $main = \Hubleto\Erp\Loader::getGlobalApp();
    (new \Hubleto\Erp\Cli\Agent\Create\TableFormViewAndController($main, $this->args(
      'Hubleto\\App\\Custom\\TestApp', // appNamespace
      'TestModel', // model
      true, // force
      true // noPrompt
    )))->run();

    $this->assertFileExists($main->projectFolder . '/src/apps/TestApp/Components/TableTestModels.tsx');
    $this->assertFileExists($main->projectFolder . '/src/apps/TestApp/Components/FormTestModel.tsx');
  }
}