<?php

namespace CeremonyCrmApp\Core;

class Router extends \ADIOS\Core\Router {
  public function __construct(\ADIOS\Core\Loader $adios) {
    parent::__construct($adios);
  }

  // public function axddRoutingGroup(
  //   string $urlRegexp,
  //   string $controllerSlug,
  //   string $viewSlug,
  //   array $commonParams,
  //   array $routes
  // ) {
  //   $newRoutes = [];

  //   foreach ($routes as $url => $item) {
  //     $regexp = '/^' . $urlRegexp . str_replace('/', '\\/', $url) . '\\/?$/';

  //     if (is_string($item)) {
  //       $newRoutes[$regexp] = [
  //         'controller' => $controllerSlug . '/' . $item ?? '',
  //         'view' => $viewSlug . '/' . $item ?? '',
  //         'params' => $commonParams,
  //       ];
  //     } else {
  //       $newRoutes[$regexp] = [
  //         'controller' => $controllerSlug . '/' . $item['controller'] ?? '',
  //         'view' => $viewSlug . '/' . $item['view'] ?? '',
  //         'params' => array_merge($commonParams, $item['params'] ?? []),
  //       ];
  //     }
  //   }

  //   $this->axddRouting($newRoutes);
  // }
}
