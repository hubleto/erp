<?php

namespace Hubleto\App\Community\Api\Controllers;

use Hubleto\App\Community\Api\Loader as ApiApp;

class OAuth extends \Hubleto\Erp\Controller
{

  public function prepareView(): void
  {
    parent::prepareView();

    $submitted = $this->router()->urlParamAsBool("submitted", false);
    $tokenEndpoint = $this->router()->urlParamAsString("tokenEndpoint", "");
    $clientId = $this->router()->urlParamAsString("clientId", "");
    $clientSecret = $this->router()->urlParamAsString("clientSecret", "");

    if ($submitted) {
      $this->config()->save('api/oauth/tokenEndpoint', $tokenEndpoint);
      $this->config()->save('api/oauth/clientId', $clientId);
      $this->config()->save('api/oauth/clientSecret', $clientSecret);
    }

    $this->viewParams['tokenEndpoint'] = $this->config()->getAsString('api/oauth/tokenEndpoint');
    $this->viewParams['clientId'] = $this->config()->getAsString('api/oauth/clientId', $this->config()->getAsString('accountUid'));
    $this->viewParams['clientSecret'] = $this->config()->getAsString('api/oauth/clientSecret');

    $this->setView('@Hubleto:App:Community:Api/OAuth.twig');
  }

}
