<?php

namespace HubletoMain\Core;

use \HubletoApp\Community\Settings\Models\UserRole;

class RecordManager extends \ADIOS\Core\EloquentRecordManager {
  public $joinManager = [];

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $main = \ADIOS\Core\Helper::getGlobalApp();

    $query = parent::prepareReadQuery($query, $level);

    $hasIdOwner = $this->model->hasColumn('id_owner');
    $hasIdResponsible = $this->model->hasColumn('id_responsible');

    $idUser = $main->auth->getUserId();

    if ($main->auth->userHasRole(UserRole::ROLE_MANAGER)) {
      if ($hasIdOwner && $hasIdResponsible) {
        $query = $query->where($this->table . '.id_owner', $idUser)->orWhere($this->table . '.id_responsible', $idUser);
      } else if ($hasIdOwner) {
        $query = $query->where($this->table . '.id_owner', $idUser);
      } else if ($hasIdResponsible) {
        $query = $query->where($this->table . '.id_responsible', $idUser);
      }
    }

    if ($main->auth->userHasRole(UserRole::ROLE_EMPLOYEE) && $hasIdOwner) {
      $query = $query->where($this->table . '.id_owner', $idUser);
    }

    if ($main->auth->userHasRole(UserRole::ROLE_ASSISTANT) && $hasIdOwner) {
      $query = $query->where($this->table . '.id_owner', $idUser);
    }

    return $query;
  }
}