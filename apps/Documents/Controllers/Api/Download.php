<?php

namespace Hubleto\App\Community\Documents\Controllers\Api;

class Download extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $file = $this->router()->urlParamAsString('file');
    $filePath = $this->env()->uploadFolder . '/' . $file;

    if (file_exists($filePath)) {
      header("Content-Type: " . mime_content_type($filePath));
      header('Content-Length: ' . filesize($filePath) );
      header("Pragma: no-cache");
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      echo file_get_contents($filePath);
      exit;
    }

    return [];
  }
}
