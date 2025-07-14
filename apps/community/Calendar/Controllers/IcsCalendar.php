<?php

namespace HubletoApp\Community\Calendar\Controllers;

use _PHPStan_ac6dae9b0\Nette\Utils\DateTime;

class IcsCalendar extends \HubletoMain\Core\Controllers\Controller
{
  public bool $hideDefaultDesktop = TRUE;
  public bool $requiresUserAuthentication = FALSE;

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'calendar', 'content' => $this->translate('Calendar') ],
    ]);
  }

  public function render(array $params): string
  {
    $ics = "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:Hubleto " . "GET ORG NAME" . "\nCALSCALE:GREGORIAN\nMETHOD:PUBLISH";
    $events = "";
    $calendarManager = $this->main->apps->community('Calendar')->calendarManager;
    foreach ($calendarManager->getCalendars() as $calendarClass => $calendar) {
      if ($calendarClass == "HubletoApp\Community\CalendarSync\Calendar") continue;
      foreach ($calendar->loadEvents((new \DateTime("now"))->format("Y-m-d H:i:s"), (new \DateTime("+1 year"))->format("Y-m-d H:i:s")) as $event) {
        $dtStart = (new \DateTime($event['start']))->setTimezone(new \DateTimeZone('UTC'))->format('Ymd\THis\Z');
        $dtEnd = (new \DateTime($event['end'] ?? $dtStart))->setTimezone(new \DateTimeZone('UTC'))->format('Ymd\THis\Z');

        if ($event['allDay'] ?? false) {
          $dtEnd = (new \DateTime(date('Y-m-d H:i:s', strtotime('tomorrow') - 1)))->setTimezone(new \DateTimeZone('UTC'))->format('Ymd\THis\Z');
        }

        $uid = uniqid() . '@example.com';

        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:$uid\r\n";
        $ics .= "SEQUENCE:0\r\n";
        $ics .= "STATUS:CONFIRMED\r\n";
        $ics .= "TRANSP:OPAQUE\r\n";
        $ics .= "SUMMARY:" . addcslashes($event['title'], ",;") . "\r\n";
        $ics .= "DTSTART:$dtStart\r\n";
        $ics .= "DTEND:$dtEnd\r\n";
        $ics .= "LOCATION:" . addcslashes($event['type'], ",;") . "\r\n";
        $ics .= "DESCRIPTION:" . addcslashes($event['title'], ",;") . "\r\n";
        $ics .= "END:VEVENT\r\n";
      }
    }

    $ics .= "END:VCALENDAR\r\n";

    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename=calendar.ics');
    return $ics;
  }

}