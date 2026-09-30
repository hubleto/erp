<?php

namespace Hubleto\App\Community\HrEmployees;

class Workflow extends \Hubleto\App\Community\Workflow\Workflow
{
  public function loadItems(int $idWorkflow, array $filters): array
  {
    $mEmployee = $this->getModel(Models\Employee::class);
    $items = $mEmployee->record->prepareReadQuery()
      ->where('hr_employees.id_workflow', $idWorkflow)
      ->with(['USER', 'TEAM']);

    $fOwner = (int) ($filters['fOwner'] ?? 0);
    if ($fOwner > 0) {
      $items->where('hr_employees.id_user', $fOwner);
    }

    $items = $items->get()?->toArray() ?? [];
    foreach ($items as $key => $item) {
      $user = $item['USER'] ?? [];
      $team = $item['TEAM'] ?? [];
      $items[$key]['_WORKFLOW_ITEM_TITLE'] = trim(($item['employee_number'] ?? '') . ' ' . ($user['nick'] ?? $user['email'] ?? ''));
      $items[$key]['_WORKFLOW_ITEM_SUBTITLE'] = trim(($item['job_title'] ?? '') . (empty($team['name']) ? '' : ' - ' . $team['name']));
      $items[$key]['_DETAIL_URL'] = 'hr-employees/' . $item['id'];
      $items[$key]['_DETAIL_VIEW'] = '@Hubleto:App:Community:Workflow/WorkflowItemDetail.twig';
    }

    return $items;
  }
}