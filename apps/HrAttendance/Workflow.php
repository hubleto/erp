<?php

namespace Hubleto\App\Community\HrAttendance;

class Workflow extends \Hubleto\App\Community\Workflow\Workflow
{
  public function loadItems(int $idWorkflow, array $filters): array
  {
    $mRecord = $this->getModel(Models\AttendanceRecord::class);
    $items = $mRecord->record->prepareReadQuery()
      ->where('hr_attendance_records.id_workflow', $idWorkflow)
      ->with(['USER', 'WORKFLOW_STEP']);

    $fOwner = (int) ($filters['fOwner'] ?? 0);
    if ($fOwner > 0) {
      $items->where('hr_attendance_records.id_user', $fOwner);
    }

    $items = $items->get()?->toArray() ?? [];
    foreach ($items as $key => $item) {
      $user = $item['USER'] ?? [];
      $step = $item['WORKFLOW_STEP'] ?? [];
      $items[$key]['_WORKFLOW_ITEM_TITLE'] = ($user['nick'] ?? $user['email'] ?? '') . ' - ' . ($item['date_worked'] ?? '');
      $items[$key]['_WORKFLOW_ITEM_SUBTITLE'] = ($step['name'] ?? '') . (empty($item['is_approved']) ? ' - ' . $this->translate('Awaiting approval') : '');
      $items[$key]['_DETAIL_URL'] = 'hr-attendance/records/' . $item['id'];
      $items[$key]['_DETAIL_VIEW'] = '@Hubleto:App:Community:Workflow/WorkflowItemDetail.twig';
    }

    return $items;
  }
}