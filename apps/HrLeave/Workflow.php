<?php

namespace Hubleto\App\Community\HrLeave;

class Workflow extends \Hubleto\App\Community\Workflow\Workflow
{
  public function loadItems(int $idWorkflow, array $filters): array
  {
    $mRequest = $this->getModel(Models\LeaveRequest::class);
    $items = $mRequest->record->prepareReadQuery()
      ->where('hr_leave_requests.id_workflow', $idWorkflow)
      ->with(['USER', 'LEAVE_TYPE', 'APPROVER']);

    $fOwner = (int) ($filters['fOwner'] ?? 0);
    if ($fOwner > 0) {
      $items->where('hr_leave_requests.id_approver', $fOwner);
    }

    $items = $items->get()?->toArray() ?? [];
    foreach ($items as $key => $item) {
      $user = $item['USER'] ?? [];
      $leaveType = $item['LEAVE_TYPE'] ?? [];
      $items[$key]['_WORKFLOW_ITEM_TITLE'] = trim(($user['nick'] ?? $user['email'] ?? '') . ' - ' . ($leaveType['name'] ?? $this->translate('Leave')));
      $items[$key]['_WORKFLOW_ITEM_SUBTITLE'] = ($item['date_from'] ?? '') . ' - ' . ($item['date_to'] ?? '') . ' - ' . ($item['days_requested'] ?? 0) . ' ' . $this->translate('days');
      $items[$key]['_DETAIL_URL'] = 'hr-leave/requests/' . $item['id'];
      $items[$key]['_DETAIL_VIEW'] = '@Hubleto:App:Community:Workflow/WorkflowItemDetail.twig';
    }

    return $items;
  }
}