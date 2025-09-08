<?php

namespace Hubleto\App\Community\Mail\Controllers;

class Settings extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'mail', 'content' => $this->translate('Mail') ],
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $settingsChanged = $this->router()->urlParamAsBool('settingsChanged');

    if ($settingsChanged) {
      $smtpHost = $this->router()->urlParamAsString('smtpHost');
      $mailApp = $this->appManager()->getApp(\Hubleto\App\Community\Mail\Loader::class);
      $mailApp->setConfigAsString('smtpHost', $smtpHost);
      $mailApp->saveConfig('smtpHost', $smtpHost);

      $this->viewParams['settingsSaved'] = true;
    }

    $this->setView('@Hubleto:App:Community:Mail/Settings.twig');
  }

}
