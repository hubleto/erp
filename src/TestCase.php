<?php

namespace Hubleto\Erp;

class TestCase extends \PHPUnit\Framework\TestCase
{

  /**
   * [Description for generateRandomIds]
   *
   * @param int $count
   * 
   * @return array
   * 
   */
  public function generateRandomIds(int $count): array
  {
    $ids = [];
    for ($i = 0; $i < $count; $i++) $ids[] = rand(-1000, 1000);
    return $ids;
  }

  /**
   * [Description for expandRoutesByVars]
   *
   * @param string $route
   * @param array $vars
   * 
   * @return array
   * 
   */
  public function expandRoutesByVars(string $route, array $vars = []): array
  {
    $routes = [];
    if (count($vars) > 0) {
      $tmpFirstKey = reset(array_keys($vars));
      foreach ($vars[$tmpFirstKey] as $k => $v) {
        $tmpRoute = $route;
        foreach ($vars as $varName => $varValues) {
          $tmpRoute = str_replace('{{ ' . $varName . ' }}', $varValues[$k] ?? null, $tmpRoute);
        }
        $routes[] = $tmpRoute;
      }
    } else {
      $routes = [ $route ];
    }

    return $routes;
  }

  /**
   * [Description for _testRouteContainsAppMainTitle]
   *
   * @param string $route
   * @param array $vars
   * 
   * @return void
   * 
   */
  public function _testRouteContainsAppMainTitle(string $route, array $vars = []): void
  {
    $renderer = \Hubleto\Erp\Loader::getGlobalApp()->renderer();
    foreach ($this->expandRoutesByVars($route, $vars) as $route) {
      $html = $renderer->render($route);
      $this->assertStringContainsString('app-main-title', $html, $route . ' does not contain app-main-title.');
    }
    
  }

  /**
   * [Description for _testRouteContainsError]
   *
   * @param string $route
   * @param array $vars
   * 
   * @return void
   * 
   */
  public function _testRouteContainsError(string $route, array $vars = []): void
  {
    $renderer = \Hubleto\Erp\Loader::getGlobalApp()->renderer();
    foreach ($this->expandRoutesByVars($route, $vars) as $route) {
      $html = $renderer->render($route);
      $this->assertStringNotContainsStringIgnoringCase('error', $html, $route . ' contains error.');
    }
    
  }

  /**
   * [Description for _testRouteRendersJson]
   *
   * @param string $route
   * @param array $vars
   * 
   * @return void
   * 
   */
  public function _testRouteRendersJson(string $route, array $vars = []): void
  {
    $renderer = \Hubleto\Erp\Loader::getGlobalApp()->renderer();
    foreach ($this->expandRoutesByVars($route, $vars) as $route) {
      $output = $renderer->render($route);
      $this->assertJson($output, $route . ' does not render JSON.');
    }
    
  }

  /**
   * [Description for testModelCrud]
   *
   * @param string $modelBaseUrl
   * 
   * @return void
   * 
   */
  public function _testModelCrud(string $modelClass, string $modelBaseUrl): void
  {
    $this->_testRouteContainsAppMainTitle($modelBaseUrl . '/add');
    $this->_testRouteContainsError($modelBaseUrl . '/add');
    $this->_testRouteContainsError(
      $modelBaseUrl . 'customers/{{ id }}',
      ['id' => $this->generateRandomIds(100)]
    );

    $this->_testRouteRendersJson('api/record/save', [
      'model' => $modelClass,
    ]);
  }


}