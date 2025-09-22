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
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    (new \Hubleto\Erp\Cli\Agent\App\Create())->setArguments($this->args(
      'Hubleto\\App\\Custom\\TestApp', // appNamespace
      true // noPrompt
    ))->run();

    $this->assertDirectoryExists($hubleto->env()->projectFolder . '/src/apps/TestApp/Controllers');
    $this->assertDirectoryExists($hubleto->env()->projectFolder . '/src/apps/TestApp/Views');
    $this->assertFileExists($hubleto->env()->projectFolder . '/src/apps/TestApp/manifest.yaml');
    $this->assertFileExists($hubleto->env()->projectFolder . '/src/apps/TestApp/Loader.php');
    $this->assertFileExists($hubleto->env()->projectFolder . '/src/apps/TestApp/Loader.tsx');
  }

  /**
   * [Description for testCreateModel]
   *
   * @return void
   * 
   */
  public function testCreateModel(): void
  {
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    (new \Hubleto\Erp\Cli\Agent\Create\Model)->setArguments($this->args(
      'Hubleto\\App\\Custom\\TestApp', // appNamespace
      'TestModel', // model
      true, // force
      true // noPrompt
    ))->run();

    $this->assertFileExists($hubleto->env()->projectFolder . '/src/apps/TestApp/Models/TestModel.php');
    $this->assertFileExists($hubleto->env()->projectFolder . '/src/apps/TestApp/Models/RecordManagers/TestModel.php');
  }

  /**
   * [Description for testCreateMvcForModel]
   *
   * @return void
   * 
   */
  public function testCreateMvcForModel(): void
  {
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    (new \Hubleto\Erp\Cli\Agent\Create\TableFormViewAndController)->setArguments($this->args(
      'Hubleto\\App\\Custom\\TestApp', // appNamespace
      'TestModel', // model
      true, // force
      true // noPrompt
    ))->run();

    $this->assertFileExists($hubleto->env()->projectFolder . '/src/apps/TestApp/Components/TableTestModels.tsx');
    $this->assertFileExists($hubleto->env()->projectFolder . '/src/apps/TestApp/Components/FormTestModel.tsx');
  }
}