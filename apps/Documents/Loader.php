<?php

namespace HubletoApp\Documents;

class Loader extends \HubletoMain\Core\App
{

  public function __construct(\HubletoMain $app)
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
    $mDocuments = new \HubletoApp\Documents\Models\Document($this->app);
    $mDocuments->dropTableIfExists()->install();
  }

}