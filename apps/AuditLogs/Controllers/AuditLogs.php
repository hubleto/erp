<?php

namespace Hubleto\App\Community\AuditLogs\Controllers;

class AuditLogs extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      // [ 'url' => 'audit-logs', 'content' => $this->translate('AuditLogs') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:AuditLogs/AuditLogs.twig');
  }

}
