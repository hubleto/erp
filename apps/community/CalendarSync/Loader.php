<?php

namespace HubletoApp\Community\CalendarSync;

class Loader extends \HubletoMain\Core\App
{

  public function __construct(\HubletoMain $app)
  {
    parent::__construct($app);
  }

  public function init(): void
  {
    $this->main->router->httpGet([
      '/^calendar-sources\/?$/' => \HubletoApp\Community\CalendarSync\Controllers\Sources::class,
    ]);

    $this->main->sidebar->addLink(1, 100, 'calendar-sources', $this->translate('Calendar sources'), 'fas fa-calendar', str_starts_with($this->main->requestedUri, 'calendar-sources'));
  }

  public function installTables(): void
  {
    $mSource = new \HubletoApp\Community\CalendarSync\Models\Source($this->main);

    $mSource->dropTableIfExists()->install();
  }

}