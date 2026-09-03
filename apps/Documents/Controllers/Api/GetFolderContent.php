<?php

namespace Hubleto\App\Community\Documents\Controllers\Api;

class GetFolderContent extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $folderUid = $this->router()->urlParamAsString('folderUid');
    $isDocumentFolder = (isset($folderUid) && $folderUid === "document_folder") ? true : false;

    $mFolder = $this->getModel(\Hubleto\App\Community\Documents\Models\Folder::class);
    $mFile = $this->getModel(\Hubleto\App\Community\Documents\Models\File::class);
    $mDocument = $this->getModel(\Hubleto\App\Community\Documents\Models\Document::class);

    $folder = [];
    $subFolders = [];
    $files = [];

    if ($isDocumentFolder) {
      $files = $mDocument->record->get()?->toArray();
    } else {
      $folder = $mFolder->record->with('PARENT_FOLDER')->where('uid', $folderUid)->first()?->toArray();
      $subFolders = $mFolder->record
        ->with('PARENT_FOLDER')
        ->whereHas('PARENT_FOLDER', function ($q) use ($folderUid, $mFolder) {
          $q->where('uid', $folderUid);
        })
        ->get()
        ?->toArray()
      ;
      $files = $mFile->record->where('id_folder', $folder['id'])->get()?->toArray();
    }

    return [
      "folderUid" => $folderUid,
      "folder" => $folder,
      "subFolders" => $subFolders,
      "files" => $files,
    ];
  }
}
