<?php

namespace Hubleto\App\Community\Workflow\Interfaces;

interface AutomatActionInterface
{
  public function execute(array $arguments): void;
}
