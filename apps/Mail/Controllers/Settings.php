<?php

namespace HubletoApp\Community\Mail\Controllers;

class Settings extends \HubletoMain\Controller
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

    $settingsChanged = $this->getRouter()->urlParamAsBool('settingsChanged');

    if ($settingsChanged) {
      $smtpHost = $this->getRouter()->urlParamAsString('smtpHost');
      $mailApp = $this->getAppManager()->getApp(\HubletoApp\Community\Mail\Loader::class);
      $mailApp->setConfigAsString('smtpHost', $smtpHost);
      $mailApp->saveConfig('smtpHost', $smtpHost);

      $this->viewParams['settingsSaved'] = true;
    }

    $this->setView('@HubletoApp:Community:Mail/Settings.twig');
  }

}
