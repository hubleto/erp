<?php

namespace HubletoApp\Community\Calendar;

class Loader extends \HubletoMain\Core\App
{

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
  }

  public function init(): void
  {
    $this->main->router->httpGet([
      '/^calendar\/?$/' => Controllers\Calendar::class,
      '/^calendar\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
    ]);

    $this->main->sidebar->addLink(1, 50, 'calendar', $this->translate('Calendar'), 'fas fa-calendar-days', str_starts_with($this->main->requestedUri, 'calendar'));
  }

}