<?php

namespace Hubleto\Erp;

use Hubleto\Framework\AuthProvider;
use Hubleto\App\Community\Auth\Controllers\SignIn;
use Hubleto\App\Community\Auth\Controllers\NotEnoughPermissions;
use Hubleto\App\Community\Desktop\Controllers\Desktop;
use Hubleto\App\Community\Settings\PermissionsManager;
use Hubleto\Framework\Controller;
use Hubleto\Framework\Exceptions\ControllerNotFound;
use Hubleto\Framework\Exceptions\GeneralException;
use Hubleto\Framework\Exceptions\NotEnoughPermissionsException;
use Hubleto\Framework\Router;

class Renderer extends \Hubleto\Framework\Renderer
{

  public function init(): void
  {
    parent::init();

    $this->twigLoader->addPath(__DIR__ . '/../views', 'hubleto-main');
    $this->twigLoader->addPath(__DIR__ . '/../apps', 'app');

    if (is_dir($this->env()->projectFolder . '/src/views')) {
      $this->twigLoader->addPath($this->env()->projectFolder . '/src/views', 'project');
    }

  }

  /**
   * Callback called before the rendering starts.
   *
   * @return void
   * 
   */
  public function onBeforeRender(): void
  {
    parent::onBeforeRender();
    $this->appManager()->onBeforeRender();
  }

  /**
   * [Description for render]
   *
   * @param string $route
   * @param array $params
   * 
   * @return string
   * 
   */
  public function render(string $route = '', array $params = []): string
  {

    try {

      $router = $this->router();

      /** @var PermissionsManager */
      $permissionManager = $this->getService(PermissionsManager::class);

      /** @var AuthProvider */
      $authProvider = $this->getService(AuthProvider::class);

      // Find-out which route is used for rendering

      if (empty($route)) $route = $router->extractRouteFromRequest();
      if (count($params) == 0) $params = $router->extractParamsFromRequest();

      $router->setRoute($route);

      // Apply routing and find-out which controller, permision and rendering params will be used
      // First, try the new routing principle with httpGet
      $routeData = $router->parseRoute(Router::HTTP_GET, $router->getRoute());

      $controllerClassName = $routeData['controller'];

      $routeVars = $routeData['vars'];
      $router->setRouteVars($params);
      $router->setRouteVars($routeVars);

      if ($router->isUrlParam('sign-out')) {
        $authProvider->signOut();
      }

      if ($router->isUrlParam('signed-out')) {
        $router->redirectTo('');
        exit;
      }

      // Check if controller exists and if it can be used
      if (empty($controllerClassName)) {
        $controllerClassName = Controllers\NotFound::class;
      };

      // Create the object for the controller
      $controllerObject = $this->getController($controllerClassName);

      // authenticate user, if any
      $this->config()->filterByUser();

      if (empty($this->permission) && !empty($controllerObject->permission)) {
        $permissionManager->setPermission($controllerObject->permission);
      }

      // Check if controller can be executed in this SAPI
      if (php_sapi_name() === 'cli') {
        /** @disregard P1014 */
        if (!$controllerClassName::$cliSAPIEnabled) {
          throw new GeneralException("Controller is not enabled in CLI interface.");
        }
      } else {
        /** @disregard P1014 */
        if (!$controllerClassName::$webSAPIEnabled) {
          throw new GeneralException("Controller is not enabled in WEB interface.");
        }
      }

      if ($controllerObject->requiresAuthenticatedUser) {
        if (!$authProvider->isUserInSession()) {
          $controllerObject = $this->getController(SignIn::class);
        }
      }

      try {

        $permissionManager->setPermission($controllerObject->permission);

        if (
          $controllerObject->requiresAuthenticatedUser
          && !$controllerObject->permittedForAllUsers
        ) {
          $permissionManager->checkPermission();
        }

      } catch (NotEnoughPermissionsException $e) {
        /** @var NotEnoughPermissions */
        $controllerObject = $this->getController(NotEnoughPermissions::class);
        $controllerObject->message = $e->getMessage();
        // $message = $e->getMessage();
        //   $message .= " Hint: Sign out at {$this->env()->projectUrl}?sign-out and sign in again or check your permissions.";
        // }
        // return "
        //   <div class='alert alert-danger' role='alert' style='z-index:99999999'>
        //     {$message}
        //   </div>
        // ";
      }

      $controllerObject->preInit();
      $controllerObject->init();
      $controllerObject->postInit();

      // All OK, rendering content...

      $return = '';

      $this->onBeforeRender();

      // Either return JSON string ...
      if ($controllerObject->returnType == Controller::RETURN_TYPE_JSON) {
        try {
          $returnArray = $controllerObject->renderJson();
        } catch (\Throwable $e) {
          http_response_code(400);

          $returnArray = [
            'status' => 'error',
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
          ];
        }
        $return = json_encode($returnArray);
      } elseif ($controllerObject->returnType == Controller::RETURN_TYPE_STRING) {
        $return = $controllerObject->renderString();
      } elseif ($controllerObject->returnType == Controller::RETURN_TYPE_NONE) {
        $controllerObject->run();
        $return = '';
      } else {
        $controllerObject->prepareView();

        $this->translationContext = $controllerObject->translationContext;
        $this->translationContextInner = $controllerObject->translationContextInner;

        $view = $controllerObject->getView();

        $contentParams = [
          'hubleto' => $this,
          'user' => $this->getService(\Hubleto\Framework\AuthProvider::class)->getUser(),
          'config' => $this->config()->get(),
          // 'routeUrl' => $router->getRoute(),
          // 'routeParams' => $this->router()->getRouteVars(),
          // 'route' => $router->getRoute(),
          // 'session' => $this->sessionManager()->get(),
          // 'controller' => $controllerObject,
          'viewParams' => $controllerObject->getViewParams(),
        ];

        if (empty($view)) {
          $contentHtml = $controllerObject->render();
        } else {
          $contentHtml = $this->renderView($view, $contentParams);
        }

        // In some cases the result of the view will be used as-is ...
        if (php_sapi_name() == 'cli' || $this->router()->urlParamAsBool('__IS_AJAX__') || $controllerObject->hideDefaultDesktop) {
          $html = $contentHtml;

          // ... But in most cases it will be "encapsulated" in the desktop.
        } else {
          $desktopControllerObject = $this->getController(Desktop::class);
          $desktopControllerObject->prepareView();

          if (!empty($desktopControllerObject->getView())) {
            $desktopParams = $contentParams;
            $desktopParams['viewParams'] = array_merge($desktopControllerObject->getViewParams(), $contentParams['viewParams']);
            $desktopParams['contentHtml'] = $contentHtml;

            $html = $this->renderView(
              $desktopControllerObject->getView(),
              $desktopParams
            );
          } else {
            $html = $contentHtml;
          }

        }

        $return = $html;
      }

      $this->onAfterRender();

      return $return;

    } catch (ControllerNotFound $e) {
      header('HTTP/1.1 400 Bad Request', true, 400);
      return $this->renderFatal($e, false);
    } catch (GeneralException $e) {
      header('HTTP/1.1 400 Bad Request', true, 400);
      return "Hubleto run failed: [".get_class($e)."] ".$e->getMessage();
    } catch (\ArgumentCountError $e) {
      echo $e->getMessage();
      header('HTTP/1.1 400 Bad Request', true, 400);
      exit;
      return '';
    } catch (\Exception $e) {
      // $error = error_get_last();
      $return = $this->renderFatal(new GeneralException($e->getMessage(), 9436), true);

      return $return;
    }
  }

}