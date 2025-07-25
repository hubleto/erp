<?php

namespace HubletoApp\Community\Documents\Controllers\Api;

class GetFolderContent extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $folderUid = $this->main->urlParamAsString('folderUid');

    $mFolder = $this->main->di->create(\HubletoApp\Community\Documents\Models\Folder::class);
    $mDocument = $this->main->di->create(\HubletoApp\Community\Documents\Models\Document::class);

    $folder = $mFolder->record->with('PARENT_FOLDER')->where('uid', $folderUid)->first()->toArray();
    $subFolders = $mFolder->record
      ->with('PARENT_FOLDER')
      ->whereHas('PARENT_FOLDER', function ($q) use ($folderUid, $mFolder) {
        $q->where('uid', $folderUid);
      })
      ->get()
      ->toArray()
    ;
    $documents = $mDocument->record->where('id_folder', $folder['id'])->get()->toArray();

    return [
      "folderUid" => $folderUid,
      "folder" => $folder,
      "subFolders" => $subFolders,
      "documents" => $documents,
    ];
  }
}
