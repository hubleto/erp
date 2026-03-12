<?php

namespace Hubleto\App\Community\Documents\Controllers;

use Hubleto\App\Community\Documents\Models\Document;
use Hubleto\App\Community\Documents\Models\Folder;

class Download extends \Hubleto\Erp\Controller
{

  public bool $requiresAuthenticatedUser = false;
  public bool $hideDefaultDesktop = true;

  public function render(): string
  {
    $folderUid = $this->router()->urlParamAsString('f');
    $documentUid = $this->router()->urlParamAsString('d');

    /** @var Folder */
    $mFolder = $this->getModel(Folder::class);

    /** @var Document */
    $mDocument = $this->getModel(Document::class);
    
    $folder = $mFolder->record->where('uid', $folderUid)->first();
    $document = $mDocument->record->where('uid', $documentUid)->first();

    $filePath = $this->env()->uploadFolder . '/' . $document->file;

    if (
      false
      || (!empty($folderUid) && !$folder)
      || !$document
      || (!empty($folderUid) && $document->id_folder != $folder->id)
      || empty($document->file)
      || !file_exists($filePath)
      || ($this->authProvider()->getUserId() <= 0 && !$document->is_public)
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
