<?php

namespace HubletoApp\Community\Messages\Controllers;

class Settings extends \HubletoMain\Core\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'messages', 'content' => $this->translate('Messages') ],
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $settingsChanged = $this->main->urlParamAsBool('settingsChanged');

    if ($settingsChanged) {
      $smtpHost = $this->main->urlParamAsString('smtpHost');
      $this->hubletoApp->setConfigAsString('smtpHost', $smtpHost);
      $this->hubletoApp->saveConfig('smtpHost', $smtpHost);

      $this->viewParams['settingsSaved'] = true;
    }

    $this->setView('@HubletoApp:Community:Messages/Settings.twig');
  }

}