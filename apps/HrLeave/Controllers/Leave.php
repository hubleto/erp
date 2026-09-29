<?php

namespace Hubleto\App\Community\HrLeave\Controllers;

class Leave extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $resources = [
      'requests' => ['Leave requests', 'LeaveRequest'],
      'types' => ['Leave types', 'LeaveType'],
      'balances' => ['Leave entitlements', 'LeaveBalance'],
    ];
    $resource = $this->router()->urlParamAsString('resource');
    if (!isset($resources[$resource])) $resource = 'requests';

    $this->viewParams['title'] = $this->translate($resources[$resource][0]);
    $this->viewParams['model'] = 'Hubleto/App/Community/HrLeave/Models/' . $resources[$resource][1];
    $this->viewParams['baseUrlSlug'] = 'hr-leave/' . $resource;
    $this->setView('@Hubleto:App:Community:HrLeave/Leave.twig');
  }
}