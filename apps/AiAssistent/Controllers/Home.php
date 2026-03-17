<?php

namespace Hubleto\App\Community\AiAssistent\Controllers;

class Home extends \Hubleto\Erp\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'aiassistent', 'content' => 'AIAssistant' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->viewParams['now'] = date('Y-m-d H:i:s');
    $this->setView('@Hubleto:App:Community:AiAssistent/Home.twig');
  }

}
