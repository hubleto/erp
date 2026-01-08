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
      $tmpKeys = array_keys($vars);
      $tmpFirstKey = reset($tmpKeys);
      if (is_array($vars[$tmpFirstKey])) {
        foreach ($vars[$tmpFirstKey] as $k => $v) {
          $tmpRoute = $route;
          foreach ($vars as $varName => $varValues) {
            $tmpRoute = str_replace('{{ ' . $varName . ' }}', $varValues[$k] ?? null, $tmpRoute);
          }
          $routes[] = $tmpRoute;
        }
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
   * [Description for _testRouteDoesNotContainError]
   *
   * @param string $route
   * @param array $vars
   * 
   * @return void
   * 
   */
  public function _testRouteDoesNotContainError(string $route, array $vars = []): void
  {
    $renderer = \Hubleto\Erp\Loader::getGlobalApp()->renderer();
    foreach ($this->expandRoutesByVars($route, $vars) as $route) {
      $html = $renderer->render($route);
      $this->assertStringNotContainsStringIgnoringCase('error', $html, $route . ' contains error.');
    }
    
  }

  /**
   * [Description for _testApiRouteReturnsJson]
   *
   * @param string $route
   * @param array $vars
   * 
   * @return void
   * 
   */
  public function _testApiRouteReturnsJson(string $route, array $vars = []): void
  {
    $renderer = \Hubleto\Erp\Loader::getGlobalApp()->renderer();
    $output = $renderer->render($route, $vars);
    $this->assertJson($output, $route . ' with ' . json_encode($vars) . ' does not render JSON. It returns: ' . $output); 
  }

  /**
   * [Description for _testCrudRouteForModel]
   *
   * @param string $modelBaseUrl
   * 
   * @return void
   * 
   */
  public function _testCrudRouteForModel(string $modelClass, string $modelBaseUrl): void
  {
    $this->_testRouteContainsAppMainTitle($modelBaseUrl . '/add');
    $this->_testRouteDoesNotContainError($modelBaseUrl . '/add');
    $this->_testRouteDoesNotContainError(
      $modelBaseUrl . 'customers/{{ id }}',
      ['id' => $this->generateRandomIds(100)]
    );

    $this->_testApiRouteReturnsJson('api/record/save', [
      'model' => $modelClass,
    ]);
  }


}