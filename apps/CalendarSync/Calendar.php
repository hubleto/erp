<?php

namespace HubletoApp\Community\CalendarSync;

use HubletoApp\Community\CalendarSync\Models\Source;

class Calendar extends \HubletoMain\Calendar
{
  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    $formattedEvents = [];
    $mSources = $this->main->di->create(Source::class);

    foreach ($mSources->record->where('active', true)->get() as $key => $source) {
      if ($source->type === 'google') {
        $url = "https://www.googleapis.com/calendar/v3/calendars/{$source->link}/events?orderBy=startTime&singleEvents=true&timeMin=" . urlencode(date('c')) . "&key={$this->main->config->getAsString('google-api-key')}";

        $jsonData = @file_get_contents($url);
        if (!$jsonData) {
          $formattedEvents[] = [
            'id' => $key, 'start' => date('Y-m-d'), 'end' => date('Y-m-d'), 'title' => 'Error fetching events', 'allDay' => true, 'type' => $source->name, "color" => "#ff0000", "backColor" => "#ff0000",
            'details' => $jsonData
          ];
          continue;
        }

        $events = json_decode($jsonData, true);
        if (isset($events['items'])) {
          foreach ($events['items'] as $event) {
            $start = $event['start']['dateTime'] ?? $event['start']['date'];
            $end = $event['end']['dateTime'] ?? $event['end']['date'];
            $formattedEvents[] = [
              'id' => $key,
              'start' => $start,
              'end' => $end,
              'title' => $event['summary'] ?? "No Title",
              'allDay' => isset($event['start']['date']),
              'type' => $source->name,
              "color" => $source->color,
              "backColor" => $source->color,
            ];
          }
        }
      } elseif ($source->type === 'ics') {
        $icsData = @file_get_contents($source->link);
        if (!$icsData) {
          $formattedEvents[] = [
            'id' => $key, 'start' => date('Y-m-d'), 'end' => date('Y-m-d'), 'title' => 'Error fetching ICS file', 'allDay' => true, 'type' => $source->name, "color" => "#ff0000", "backColor" => "#ff0000",
          ];
          continue;
        }

        preg_match_all('/BEGIN:VEVENT.*?END:VEVENT/s', $icsData, $matches);
        foreach ($matches[0] as $event) {
          preg_match('/DTSTART(?:;.*)?:(\d{8}(?:T\d{6}Z?)?)/', $event, $startMatch);
          preg_match('/DTEND(?:;.*)?:(\d{8}(?:T\d{6}Z?)?)/', $event, $endMatch);
          preg_match('/SUMMARY(?:;.*)?:(.*)/', $event, $summaryMatch);

          $start = isset($startMatch[1]) ? date('Y-m-d\TH:i:s', strtotime($startMatch[1])) : date('Y-m-d\TH:i:s');
          $end = isset($endMatch[1]) ? date('Y-m-d\TH:i:s', strtotime($endMatch[1])) : $start;
          $summary = isset($summaryMatch[1]) ? trim($summaryMatch[1]) : 'No Title';

          $formattedEvents[] = [
            'id' => $key,
            'start' => $start,
            'end' => $end,
            'title' => $summary,
            'allDay' => strpos($start, 'T') === false,
            'type' => $source->name,
            "color" => $source->color,
            "backColor" => $source->color,
          ];
        }
      }
    }

    return $formattedEvents;
  }

}
