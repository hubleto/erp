<?php

namespace Hubleto\App\Community\Workflow\Automats\Actions;

use Hubleto\App\Community\Workflow\Interfaces\AutomatActionInterface;
use Hubleto\Erp\Core;

class LogMessage extends Core implements AutomatActionInterface
{

  /**
   * [Description for execute]
   *
   * @param array $arguments
   * 
   * @return void
   * 
   */
  public function execute(array $arguments): void
  {
    $message = (string) $arguments['message'] ?? '';
    $this->logger()->info("WorkflowProcessor: " . $message);
  }
}