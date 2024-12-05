<?php

namespace CeremonyCrmApp\Modules\Sales\Core;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^sales\/?$/' => Controllers\Home::class,
    ]);

    // $router(
    //   'sales',
    //   'CeremonyCrmApp/Modules/Sales/Sales/Controllers',
    //   '@app/Modules/Sales/Sales/Views',
    //   [
    //     'idAccount' => '$1',
    //   ],
    //   [
    //     '' => 'Home',
    //     '/leads' => 'Leads',
    //     '/deals' => 'Deals',
    //     '/convert-lead' => 'ConvertLead',
    //     '/convert-lead' => 'ConvertLead',
    //     '/change-pipeline' => 'ChangePipeline',
    //     '/change-pipeline-step' => 'ChangePipelineStep',
    //   ]
    // );
  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 80100, 'sales', $this->app->translate('Sales'), 'fas fa-money-bill');

    if (str_starts_with($this->app->requestedUri, 'sales')) {
      $sidebar->addHeading1(2, 10200, $this->app->translate('Sales'));
      $sidebar->addLink(2, 10201, 'sales', $this->app->translate('Pipeline'), 'fas fa-timeline');
    }
  }

  public function generateTestData()
  {}
}