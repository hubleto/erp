<?php

namespace Hubleto\App\Community\HrAttendance\Controllers;

class Attendance extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $resources = [
      'records' => ['Attendance records', 'AttendanceRecord'],
      'shifts' => ['Shift schedule', 'Shift'],
    ];
    $resource = $this->router()->urlParamAsString('resource');
    if (!isset($resources[$resource])) $resource = 'records';

    $this->viewParams['title'] = $this->translate($resources[$resource][0]);
    $this->viewParams['model'] = 'Hubleto/App/Community/HrAttendance/Models/' . $resources[$resource][1];
    $this->viewParams['baseUrlSlug'] = 'hr-attendance/' . $resource;
    $this->setView('@Hubleto:App:Community:HrAttendance/Attendance.twig');
  }
}