<?php

namespace Hubleto\App\Community\HrRecruitment\Controllers;

class Recruitment extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $resources = [
      'job-openings' => ['Job openings', 'JobOpening'],
      'candidates' => ['Candidates', 'Candidate'],
      'applications' => ['Applications', 'Application'],
      'interviews' => ['Interviews', 'Interview'],
      'employment-types' => ['Employment types', 'EmploymentType'],
      'work-locations' => ['Work locations', 'WorkLocation'],
      'opening-dates' => ['Opening dates', 'OpeningDate'],
    ];
    $resource = $this->router()->urlParamAsString('resource');
    if (!isset($resources[$resource])) $resource = 'job-openings';

    $this->viewParams['title'] = $this->translate($resources[$resource][0]);
    $this->viewParams['model'] = 'Hubleto/App/Community/HrRecruitment/Models/' . $resources[$resource][1];
    $this->viewParams['baseUrlSlug'] = 'hr-recruitment/' . $resource;
    $lookupViews = [
      'employment-types' => 'EmploymentTypes.twig',
      'work-locations' => 'WorkLocations.twig',
      'opening-dates' => 'OpeningDates.twig',
    ];
    $view = $lookupViews[$resource] ?? 'Recruitment.twig';
    $this->setView('@Hubleto:App:Community:HrRecruitment/' . $view);
  }
}