<?php

namespace Hubleto\App\Community\Projects;

class Calendar extends \Hubleto\App\Community\Calendar\Calendar
{

  public function getCalendarConfig(): array
  {
    return [
      'position' => 7,
      'color' => '#20689f',
      'title' => $this->translate('Projects'),
      'addNewActivityButtonText' => $this->translate('Add new activity linked to Projects'),
      'icon' => 'fas fa-calendar',
      'formComponent' => 'ProjectsFormActivity',
    ];
  }

  public function loadEvent(int $id): array
  {
    return $this->prepareLoadActivityQuery($this->getModel(Models\ProjectActivity::class), $id)->first()?->toArray();
  }

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    $events = [];

    $idProject = $this->router()->urlParamAsInteger('idProject');
    $mMilestone = $this->getModel(Models\Milestone::class);

    // milestones
    $milestones = $mMilestone->record->prepareReadQuery()
      ->whereRaw("`{$mMilestone->table}`.`date_due` >= ? AND `{$mMilestone->table}`.`date_due` <= ?", [$dateStart, $dateEnd]);

    if ($idProject > 0) {
      $milestones = $milestones->where($mMilestone->table . '.id_project', $idProject);
    }

    if (isset($filter['fCompleted']) && $filter['fCompleted'] > 0) {
      $milestones = $milestones->where($mMilestone->table . '.is_closed', $filter['fCompleted'] == 2);
    }

    $milestones = $milestones->get();

    foreach ($milestones as $milestone) { //@phpstan-ignore-line
      $events[] = [
        'id' => (int) ($tasmilestonek->id ?? 0),
        'start' => date("Y-m-d", strtotime($milestone->date_due)),
        'end' => date("Y-m-d", strtotime($milestone->date_due)),
        'allDay' => true,
        'title' => 'MILESTONE ' . $milestone->title,
        'color' => '#20689f',
        'source' => 'projects',
        'id_owner' => 0,
        'completed' => 0,
        'url' => 'projects/milestones/' . $milestone->id,
      ];
    }

    return $events;
  }

}
