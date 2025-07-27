<?php

namespace HubletoApp\Community\Calendar\Controllers;

use _PHPStan_ac6dae9b0\Nette\Utils\DateTime;
use HubletoApp\Community\Calendar\Models\RecordManagers\SharedCalendar;

class IcsCalendar extends \HubletoMain\Controller
{
  public bool $hideDefaultDesktop = true;
  public bool $requiresUserAuthentication = false;

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'calendar', 'content' => $this->translate('Calendar') ],
    ]);
  }

  public function render(array $params): string
  {
    $ics = "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:Hubleto " . $this->main->config->getAsString('accountFullName') . "\nCALSCALE:GREGORIAN\nMETHOD:PUBLISH\n";
    $events = "";
    $calendarManager = $this->main->apps->community('Calendar')->calendarManager;

    $calendarKey = $this->main->getUrlParams()['key'];

    $mSharedCalendars = new SharedCalendar();
    $sharedCalendar = $mSharedCalendars->where('share_key', $calendarKey)->where('enabled', true)->first();

    if ($sharedCalendar == null) {
      $this->main->router->redirectTo('/404');
    }
    $calendarSource = $sharedCalendar->get('calendar')[0]['calendar'];

    // permissions
    $viewDetails = $sharedCalendar->get('view_details')->toArray()[0]['view_details'];
    $dateFrom = $sharedCalendar->get('date_from')->toArray()[0]['date_from'] ?? (new \DateTime("now"))->format("Y-m-d H:i:s");
    $dateTo = $sharedCalendar->get('date_to')->toArray()[0]['date_to'] ?? (new \DateTime("+1 year"))->format("Y-m-d H:i:s");

    $calendar = $calendarManager->getCalendar($calendarSource);

    foreach ($calendar->loadEvents($dateFrom, $dateTo) as $event) {
      $dtStart = (new \DateTime($event['start']))->setTimezone(new \DateTimeZone('UTC'))->format('Ymd\THis\Z');

      if ($event['allDay'] ?? false) {
        $dtEnd = (new \DateTime($event['start']))
          ->setTimezone(new \DateTimeZone('UTC'))
          ->setTime(23, 59, 59)
          ->format('Ymd\THis\Z');
      } else {
        $dtEnd = (new \DateTime($event['end'] ?? $event['start']))
          ->setTimezone(new \DateTimeZone('UTC'))
          ->format('Ymd\THis\Z');
      }

      $uid = uniqid();

      $ics .= "BEGIN:VEVENT\r\n";
      $ics .= "UID:$uid\r\n";
      $ics .= "SEQUENCE:0\r\n";
      $ics .= "STATUS:CONFIRMED\r\n";
      $ics .= "TRANSP:OPAQUE\r\n";
      if ($viewDetails) {
        $ics .= "SUMMARY:" . addcslashes($event['title'], ",;") . "\r\n";
      }
      $ics .= "DTSTART:$dtStart\r\n";
      $ics .= "DTEND:$dtEnd\r\n";
      if ($viewDetails) {
        $ics .= "LOCATION:" . addcslashes($event['type'], ",;") . "\r\n";
        $ics .= "DESCRIPTION:" . addcslashes($event['title'], ",;") . "\r\n";
      }
      $ics .= "END:VEVENT\r\n";
    }

    $ics .= "END:VCALENDAR\r\n";

    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename=calendar.ics');
    return $ics;
  }

}
