<?php

namespace HubletoApp\Community\CalendarSync\Controllers;

class Home extends \HubletoMain\Core\Controller {
  public function prepareView(): void {
    parent::prepareView();
    $this->viewParams['now'] = date('Y-m-d H:i:s');
    $this->setView('@HubletoApp:Community:CalendarSync/home.twig');

    // $mSource = new Source($this->app);
    // $mSource->install();
  }
}