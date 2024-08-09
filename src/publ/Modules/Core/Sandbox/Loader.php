<?php

namespace CeremonyCrmApp\Modules\Core\Sandbox;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);

    $this->registerModel(\CeremonyCrmApp\Modules\Core\Sandbox\Models\TestModel1::class);
  }

  public function addRouting(\CeremonyCrmApp\Core\Router $router)
  {
    $router->addRoutingGroup(
      'sandbox',
      'CeremonyCrmApp/Modules/Core/Sandbox/Controllers',
      'CeremonyCrmApp/Modules/Core/Sandbox/Views',
      [],
      [
        '' => 'Dashboard',
        '/test-model-1' => 'TestModel1',
      ]
    );
  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 110100, 'sandbox', 'Sandbox', 'fas fa-cog');

    if (str_starts_with($this->app->requestedUri, 'sandbox')) {
      $sidebar->addHeading1(2, 110200, 'Sandbox');
      $sidebar->addLink(2, 110201, 'sandbox/test-model-1','Test Model 1', 'fas fa-user');
    }
  }

  public function generateTestData()
  {
    $mTestModel1 = new Models\TestModel1($this->app);
    $mTestModel1->install();
    // $mSetting->eloquent->create(['key' => 'test/setting/example', 'value' => rand(1000, 9999)]);
  }
}
