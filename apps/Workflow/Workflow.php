<?php

namespace Hubleto\App\Community\Workflow;

class Workflow extends \Hubleto\Erp\Core
{
  public \Hubleto\Framework\Interfaces\AppInterface $app;

  public function loadItems(int $idWorkflow, array $filters): array
  {
    return [];
  }
}
