<?php

namespace HubletoMain\Core;

use \HubletoApp\Community\Settings\Models\UserRole;

class RecordManager extends \ADIOS\Core\EloquentRecordManager {
  public $joinManager = [];

  public function getPermissions(array $record): array
  {

    // by default, restrict all CRUD operations

    $permissions = [false, false, false, false];

    // prepare some variables

    $main = \ADIOS\Core\Helper::getGlobalApp();
    $idUser = $main->auth->getUserId();

    $hasIdOwner = isset($record['id_owner']);
    $hasIdResponsible = isset($record['id_responsible']);

    $isOwner = false;
    if ($hasIdOwner) $isOwner = $record['id_owner'] == $idUser;

    $isResponsible = false;
    if ($hasIdResponsible) $isResponsible = $record['id_responsible'] == $idUser;

    // enable permissions by certain criteria
    $canRead = false;
    $canModify = false;

    if ($main->auth->userHasRole(UserRole::ROLE_ADMINISTRATOR)) {
      $canRead = true;
      $canModify = true;
    } if ($main->auth->userHasRole(UserRole::ROLE_CHIEF_OFFICER)) {
      // CxO can do anything except for modifying config and settings

      $canRead = true;
      $canModify = true;

      if (str_starts_with($this->model->fullName, 'ADIOS/Core/Config')) {
        $canModify = false;
      }
    } else if ($main->auth->userHasRole(UserRole::ROLE_MANAGER)) {
      // Manager can:
      //   - read only records where he/she is owner or responsible
      //   - modify only records where he/she is owner

      if ($hasIdResponsible && $isResponsible || !$hasIdResponsible) $canRead = true;

      if ($hasIdOwner && $isOwner || !$hasIdOwner) {
        $canRead = true;
        $canModify = true;
      }

      $permissions = [$canRead, $canModify, $canModify, $canModify];
    } else if ($main->auth->userHasRole(UserRole::ROLE_EMPLOYEE)) {
      // Employee can:
      //   - read/modify only records where he/she is owner

      if ($hasIdOwner && $isOwner || !$hasIdOwner) {
        $canRead = true;
        $canModify = true;
      }

    } else if ($main->auth->userHasRole(UserRole::ROLE_ASSISTANT)) {
      // Assistant can:
      //   - read/modify only records where he/she is owner

      if ($hasIdOwner && $isOwner || !$hasIdOwner) $canRead = true;
    } else if ($main->auth->userHasRole(UserRole::ROLE_EXTERNAL)) {
      // Externals cannot do anything by default
    }

    $permissions = [$canModify, $canRead, $canModify, $canModify];

    // merge default permissions with user configured

    $permissions = [
      $permissions[0] || $main->permissions->granted($this->model->permission . ':Create'),
      $permissions[1] || $main->permissions->granted($this->model->permission . ':Read'),
      $permissions[2] || $main->permissions->granted($this->model->permission . ':Update'),
      $permissions[3] || $main->permissions->granted($this->model->permission . ':Delete'),
    ];

    return $permissions;
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $main = \ADIOS\Core\Helper::getGlobalApp();

    $query = parent::prepareReadQuery($query, $level);

    $hasIdOwner = $this->model->hasColumn('id_owner');
    $hasIdResponsible = $this->model->hasColumn('id_responsible');

    $idUser = $main->auth->getUserId();

    if ($main->auth->userHasRole(UserRole::ROLE_MANAGER)) {
      if ($hasIdOwner && $hasIdResponsible) {
        $query = $query->where(function($q) use ($idUser) {
          $q
            ->where($this->table . '.id_owner', $idUser)
            ->orWhere($this->table . '.id_responsible', $idUser);
        });
      } else if ($hasIdOwner) {
        $query = $query->where($this->table . '.id_owner', $idUser);
      } else if ($hasIdResponsible) {
        $query = $query->where($this->table . '.id_responsible', $idUser);
      }
    } else if ($main->auth->userHasRole(UserRole::ROLE_EMPLOYEE) && $hasIdOwner) {
      $query = $query->where($this->table . '.id_owner', $idUser);
    } else if ($main->auth->userHasRole(UserRole::ROLE_ASSISTANT) && $hasIdOwner) {
      $query = $query->where($this->table . '.id_owner', $idUser);
    }

    return $query;
  }
}