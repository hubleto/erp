<?php

/*
  This hook redirects views. Use it, if you want to
  make your custom view for already existing controller
  and route. Uncomment examples and modify to your needs.
*/

namespace HubletoMain\Hook;

class ViewRedirects extends \HubletoMain\Core\Hook
{

  // Uncomment and modify the `run()` method to customize
  // look & feel of your Hubleto.

  // public function run(string $event, array $args): void
  // {
  //   if ($event == 'controller:set-view') {
  //     list($controller, $view, $viewParams) = $args;
  //     switch ($view) {
  //       case '@HubletoApp:Community:Settings/Dashboard.twig': $controller->setView('@HubletoApp:Custom:Settings/Dashboard.twig'); break;
  //     }
  //   }
  // }

}