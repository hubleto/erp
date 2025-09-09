<?php

namespace Hubleto\App\Community\Workflow;

class Workflow extends \Hubleto\Framework\Core
{
  public \Hubleto\Framework\Interfaces\AppInterface $app;

  public function loadItems(int $idWorkflow, array $filters): array
  {
    return [];
  }
}
