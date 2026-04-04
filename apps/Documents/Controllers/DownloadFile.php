<?php

namespace Hubleto\App\Community\Documents\Controllers;

use Hubleto\App\Community\Documents\Models\File;
use Hubleto\App\Community\Documents\Models\Folder;

class DownloadFile extends \Hubleto\Erp\Controller
{

  public bool $requiresAuthenticatedUser = false;
  public bool $hideDefaultDesktop = true;

  public function render(): string
  {
    $folderUid = $this->router()->urlParamAsString('fld');
    $fileUid = $this->router()->urlParamAsString('fil');

    /** @var Folder */
    $mFolder = $this->getModel(Folder::class);

    /** @var File */
    $mFile = $this->getModel(File::class);
    
    $folder = $mFolder->record->where('uid', $folderUid)->first();
    $file = $mFile->record->where('uid', $fileUid)->first();

    $filePath = $this->env()->uploadFolder . '/' . $file?->file;

    if (
      false
      || (!empty($folderUid) && !$folder)
      || !$file
      || (!empty($folderUid) && $file->id_folder != $folder->id)
      || empty($file->file)
      || !file_exists($filePath)
      || ($this->authProvider()->getUserId() <= 0 && !$file->is_public)
    ) {
      http_response_code(404);
      return '';
    }

    header("Content-Type: " . mime_content_type($filePath));
    header('Content-Length: ' . filesize($filePath) );
    header("Pragma: no-cache");
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    return file_get_contents($filePath);

  }
}
