<?php

namespace HubletoApp\Community\Documents;

class Loader extends \HubletoMain\Core\App
{

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);

    $this->registerModel(Models\Document::class);
  }

  public function init(): void
  {
    $this->main->router->httpGet([
      '/^documents\/?$/' => Controllers\Documents::class,
    ]);

    $this->main->sidebar->addLink(1, 700, 'documents', $this->translate('Documents'), 'fa-regular fa-file', str_starts_with($this->main->requestedUri, 'documents'));
  }

  public function installTables(): void
  {
    $mDocuments = new \HubletoApp\Community\Documents\Models\Document($this->main);
    $mDocuments->dropTableIfExists()->install();
  }

}