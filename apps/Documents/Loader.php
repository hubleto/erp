<?php

namespace CeremonyCrmMod\Documents;

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

    $this->app->sidebar->addLink(1, 700, 'documents', $this->translate('Documents'), 'fa-regular fa-file', str_starts_with($this->app->requestedUri, 'documents'));
  }

  public function installTables()
  {
    $mDocuments = new \CeremonyCrmMod\Documents\Models\Document($this->app);
    $mDocuments->dropTableIfExists()->install();
  }

}