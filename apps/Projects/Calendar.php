<?php

namespace Hubleto\App\Community\Projects;

class Calendar extends \Hubleto\App\Community\Calendar\Calendar
{
  public array $calendarConfig = [
    "title" => "Projects",
    "addNewActivityButtonText" => "Add new activity linked to Projects",
    "icon" => "fas fa-calendar",
    "formComponent" => "ProjectsFormActivity"
  ];

  public function loadEvent(int $id): array
  {
    return $this->prepareLoadActivityQuery($this->getModel(Models\ProjectActivity::class), $id)->first()?->toArray();
  }

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    $idProject = $this->router()->urlParamAsInteger('idProject');
    $mProjectActivity = $this->getModel(Models\ProjectActivity::class);
    $activities = $this->prepareLoadActivitiesQuery($mProjectActivity, $dateStart, $dateEnd, $filter)->with('PROJECT.CUSTOMER');
    if ($idProject > 0) {
      $activities = $activities->where("id_project", $idProject);
    }

    $events = $this->convertActivitiesToEvents(
      'projects',
      $activities->get()?->toArray(),
      function (array $activity) {
        if (isset($activity['PROJECT'])) {
          $project = $activity['PROJECT'];
          $customer = $project['CUSTOMER'] ?? [];
          return 'Project ' . $project['identifier'] . ' ' . $project['title'] . (isset($customer['name']) ? ', ' . $customer['name'] : '');
        } else {
          return '';
        }
      }
    );

    return $events;
  }

}
