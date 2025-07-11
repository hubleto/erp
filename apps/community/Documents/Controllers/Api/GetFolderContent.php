<?php

namespace HubletoApp\Community\Documents\Controllers\Api;

class GetFolderContent extends \HubletoMain\Core\Controllers\Controller
{
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;
  public bool $permittedForAllUsers = true;

  public function renderJson(): ?array
  {
    $folderUid = $this->main->urlParamAsString('folderUid');

    $mFolder = new \HubletoApp\Community\Documents\Models\Folder($this->main);
    $mDocument = new \HubletoApp\Community\Documents\Models\Document($this->main);

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
