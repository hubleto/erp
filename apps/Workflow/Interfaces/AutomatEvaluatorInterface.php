<?php

namespace Hubleto\App\Community\Workflow\Interfaces;

interface AutomatEvaluatorInterface
{
  public function matches(array $arguments): bool;
}
