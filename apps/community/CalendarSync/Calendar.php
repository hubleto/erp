<?php

namespace HubletoApp\Community\CalendarSync;

use HubletoApp\Community\CalendarSync\Models\Source;

class Calendar extends \HubletoMain\Core\Calendar {

    public function loadEvents(): array
    {
        $formattedEvents = [];

        $mSources = new Source($this->main);

        foreach ($mSources->eloquent->where('type', 'google')->get() as $source) {

          // Build API URL
          $url = "https://www.googleapis.com/calendar/v3/calendars/{$source->link}/events?orderBy=startTime&singleEvents=true&timeMin=" . urlencode(date('c')) . "&key={$this->main->getConfig('google-api-key')}";

          // Fetch Data
          $jsonData = @file_get_contents($url);

          if (!$jsonData) {
            return [
              ['id' => 101, 'start' => '2025-02-15', 'end' => '2025-02-15', 'title' => 'Error fetching events', 'allDay' => true, 'type' => "Error", "color" => "#ff0000", "backColor" => "#ff0000"],
            ];
          }

          $events = json_decode($jsonData, true);

          // Parse Events
          if (isset($events['items'])) {
            foreach ($events['items'] as $event) {
              $start = $event['start']['dateTime'] ?? $event['start']['date'];
              $end = $event['end']['dateTime'] ?? $event['end']['date'];
              $formattedEvents[] = [
                'id' => $event['id'],
                'start' => $start,
                'end' => $end,
                'title' => $event['summary'] ?? "No Title",
                'allDay' => isset($event['start']['date']),
                'type' => "Google Calendar Event",
                "color" => "#4285F4",
                "backColor" => "#4285F4",
              ];
            }
          }
        }

        return $formattedEvents;
    }

}
