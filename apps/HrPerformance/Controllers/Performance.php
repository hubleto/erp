<?php

namespace Hubleto\App\Community\HrPerformance\Controllers;

class Performance extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $resources = [
      'goals' => ['Employee goals', 'Goal'],
      'reviews' => ['Performance reviews', 'Review'],
      'courses' => ['Learning courses', 'Course'],
      'learning' => ['Learning assignments', 'LearningAssignment'],
    ];
    $resource = $this->router()->urlParamAsString('resource');
    if (!isset($resources[$resource])) $resource = 'goals';

    $this->viewParams['title'] = $this->translate($resources[$resource][0]);
    $this->viewParams['model'] = 'Hubleto/App/Community/HrPerformance/Models/' . $resources[$resource][1];
    $this->viewParams['baseUrlSlug'] = 'hr-performance/' . $resource;
    $this->setView('@Hubleto:App:Community:HrPerformance/Performance.twig');
  }
}