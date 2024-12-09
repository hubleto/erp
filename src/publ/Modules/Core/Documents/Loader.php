<?php

namespace CeremonyCrmApp\Modules\Core\Documents;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);

    $this->registerModel(Models\Document::class);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^documents\/?$/' => Controllers\Documents::class,
    ]);

    // $router(
    //   'documents',
    //   'CeremonyCrmApp/Modules/Core/Documents/Controllers',
    //   '@app/Modules/Core/Documents/Views',
    //   [
    //     'idAccount' => '$1',
    //   ],
    //   [
    //     '' => 'Documents',
    //   ]
    // );

  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 70100, 'documents', $this->app->translate('Documents'), 'fa-regular fa-file');
  }

  public function installTables()
  {
    $mDocuments = new \CeremonyCrmApp\Modules\Core\Documents\Models\Document($this->app);
    $mDocuments->dropTableIfExists()->install();
  }

}