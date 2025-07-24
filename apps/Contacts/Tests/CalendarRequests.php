<?php

namespace HubletoApp\Community\Customers\Tests;

class CalendarRequests extends \Hubleto\Framework\AppTest
{
  public function run(): void
  {
    $this->main->setUrlParam('start', date('Y-m-d', strtotime('-' . (string) rand(5, 10) . ' days')));
    $this->main->setUrlParam('end', date('Y-m-d', strtotime('-' . (string) rand(20, 30) . ' days')));
    $events = (new \HubletoApp\Community\Customers\Controllers\Api\GetCalendarEvents($this->main))->renderJson();

    $this->assert('events is array', is_array($events));

  }

}
