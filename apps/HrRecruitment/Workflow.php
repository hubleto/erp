<?php

namespace Hubleto\App\Community\HrRecruitment;

class Workflow extends \Hubleto\App\Community\Workflow\Workflow
{
  public function loadItems(int $idWorkflow, array $filters): array
  {
    $mApplication = $this->getModel(Models\Application::class);
    $items = $mApplication->record->prepareReadQuery()
      ->where('hr_applications.id_workflow', $idWorkflow)
      ->with(['JOB_OPENING', 'CANDIDATE', 'WORKFLOW_STEP']);

    $fOwner = (int) ($filters['fOwner'] ?? 0);
    if ($fOwner > 0) {
      $items->whereHas('JOB_OPENING', function ($query) use ($fOwner) {
        $query->where('id_hiring_manager', $fOwner);
      });
    }

    $items = $items->get()?->toArray() ?? [];
    foreach ($items as $key => $item) {
      $candidate = $item['CANDIDATE'] ?? [];
      $opening = $item['JOB_OPENING'] ?? [];
      $candidateName = trim(($candidate['first_name'] ?? '') . ' ' . ($candidate['last_name'] ?? ''));
      $items[$key]['_WORKFLOW_ITEM_TITLE'] = $candidateName ?: $this->translate('Application') . ' #' . $item['id'];
      $step = $item['WORKFLOW_STEP'] ?? [];
      $items[$key]['_WORKFLOW_ITEM_SUBTITLE'] = ($opening['title'] ?? '') . ' - ' . ($step['name'] ?? '');
      $items[$key]['_DETAIL_URL'] = 'hr-recruitment/applications/' . $item['id'];
      $items[$key]['_DETAIL_VIEW'] = '@Hubleto:App:Community:Workflow/WorkflowItemDetail.twig';
    }

    return $items;
  }
}