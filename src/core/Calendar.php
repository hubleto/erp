<?php

namespace HubletoMain\Core;

class Calendar implements \ADIOS\Core\Testable {

  public \HubletoMain $main;

/**
 * Specifies what Activity Form component will be opened and what title should be used for a new button in the `FormActivitySelector.tsx` component
 * @var array{"title": string, "formComponent": string}
 * */
  public array $activitySelectorConfig = [
    "addNewActivityButtonText" => "",
    "formComponent" => ""
  ];

  protected string $color = 'blue';

  public function __construct(\HubletoMain $main) {
    $this->main = $main;
  }

  public function setColor(string $color): void
  {
    $this->color = $color;
  }

  public function getColor(): string
  {
    return $this->color;
  }

  public function loadEvents(string $dateStart, string $dateEnd): array
  {
    return [];
  }

  public function convertActivitiesToEvents(string $source, array $activities, \Closure $detailsCallback): array
  {
    $events = [];

    foreach ($activities as $key => $activity) { //@phpstan-ignore-line

      $dStart = (string) ($activity['date_start'] ?? '');
      $tStart = (string) ($activity['time_start'] ?? '');
      $dEnd = (string) ($activity['date_end'] ?? '');
      $tEnd = (string) ($activity['time_end'] ?? '');

      $events[$key]['id'] = (int) ($activity['id'] ?? 0);

      if ($tStart != '') $events[$key]['start'] = $dStart . " " . $tStart;
      else $events[$key]['start'] = $dStart;

      if ($dEnd != '') {
        if ($tEnd != '') $events[$key]['end'] = $dEnd . " " . $tEnd;
        else $events[$key]['end'] = $dEnd;
      } else if ($tEnd != '') {
        $events[$key]['end'] = $dStart . " " . $tEnd;
      }

      $longerThanDay = (!empty($dStart) && !empty($dEnd) && ($dStart != $dEnd));

      // fix for fullCalendar not showing the last date of an event longer than one day
      if ((!empty($dStart) && !empty($dEnd) && $longerThanDay)) {
        $events[$key]['end'] = date("Y-m-d", strtotime("+ 1 day", strtotime($dEnd)));
      }

      $events[$key]['allDay'] = ($activity['all_day'] ?? 0) == 1 || $tStart == null ? true : false || $longerThanDay;
      $events[$key]['title'] = (string) ($activity['subject'] ?? '');
      $events[$key]['backColor'] = (string) ($activity['color'] ?? '');
      $events[$key]['color'] = $this->color;
      $events[$key]['type'] = (int) ($activity['activity_type'] ?? 0);
      $events[$key]['source'] = $source; //'customers';
      $events[$key]['details'] = $detailsCallback($activity);
    }

    return $events;
  }

  public function assert(string $assertionName, bool $assertion): void
  {
    if ($this->main->testMode && !$assertion) {
      throw new \ADIOS\Core\Exceptions\TestAssertionFailedException('TEST FAILED: Assertion [' . $assertionName . '] not fulfilled in ' . get_parent_class($this));
    }
  }

}