<?php

namespace Hubleto\App\Community\Settings\Controllers;

use Hubleto\Framework\Helper;

class LogViewer extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $logFile = $this->router()->urlParamAsString('f');

    $logFolder = $this->config()->getAsString('logFolder');

    $this->viewParams['logFiles'] = Helper::scanDirRecursively($logFolder);

    if (!empty($logFile) && strpos($logFile, '..') === false && file_exists($logFolder . '/' . $logFile)) {
      $this->viewParams['logFile'] = $logFile;
      $this->viewParams['logFileContent'] = file_get_contents($logFolder . '/' . $logFile);
    }

    $this->setView('@Hubleto:App:Community:Settings/LogViewer.twig');
  }

}
