<?php

namespace Hubleto\App\Community\HrPerformance;

class Workflow extends \Hubleto\App\Community\Workflow\Workflow
{
  public function loadItems(int $idWorkflow, array $filters): array
  {
    $mReview = $this->getModel(Models\Review::class);
    $items = $mReview->record->prepareReadQuery()
      ->where('hr_reviews.id_workflow', $idWorkflow)
      ->with(['EMPLOYEE', 'REVIEWER']);

    $fOwner = (int) ($filters['fOwner'] ?? 0);
    if ($fOwner > 0) {
      $items->where('hr_reviews.id_reviewer', $fOwner);
    }

    $items = $items->get()?->toArray() ?? [];
    foreach ($items as $key => $item) {
      $employee = $item['EMPLOYEE'] ?? [];
      $items[$key]['_WORKFLOW_ITEM_TITLE'] = trim(($employee['nick'] ?? $employee['email'] ?? '') . ' - ' . ($item['period'] ?? ''));
      $items[$key]['_WORKFLOW_ITEM_SUBTITLE'] = ($item['status'] ?? '') . (empty($item['date_reviewed']) ? '' : ' - ' . $item['date_reviewed']);
      $items[$key]['_DETAIL_URL'] = 'hr-performance/reviews/' . $item['id'];
      $items[$key]['_DETAIL_VIEW'] = '@Hubleto:App:Community:Workflow/WorkflowItemDetail.twig';
    }

    return $items;
  }
}