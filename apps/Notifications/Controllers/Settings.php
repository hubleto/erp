<?php

namespace Hubleto\App\Community\Notifications\Controllers;

use Hubleto\App\Community\Notifications\Loader as NotificationsApp;

class Settings extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $submitted = $this->router()->urlParamAsBool('submitted');

    if ($submitted) {
      $sendDailyDigest = $this->router()->urlParamAsBool('sendDailyDigest');
      $this->config()->forApp(NotificationsApp::class)->saveForUser('sendDailyDigest', $sendDailyDigest);
    } else {
      $sendDailyDigest = $this->config()->forApp(NotificationsApp::class)->getAsBool('sendDailyDigest', true);
    }

    $this->viewParams['sendDailyDigest'] = $sendDailyDigest;

    $this->setView('@Hubleto:App:Community:Notifications/Settings.twig');
  }

}
