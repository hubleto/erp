<?php

namespace CeremonyCrmApp\Core;

class Permissions extends \ADIOS\Core\Permissions {
  public array $administratorRoles = [\CeremonyCrmApp\Modules\Core\Settings\Models\UserRole::ROLE_ADMINISTRATOR];

  public function loadPermissions(): array {
    $permissions = parent::loadPermissions();

    // $permissions[\EMonitorApp\Models\User::ROLE_ENGINEER] = [
    //   'EMonitorApp/App/Dashboard',
    //   'EMonitorApp/App/Lokalita',
    //   'EMonitorApp/App/Zariadenie',
    //   // 'EMonitorApp/App/ImportDataloggerov',
    //   'EMonitorApp/App/Zariadenie/PridatUpravit',
    //   // 'EMonitorApp/App/Zariadenie/Porovnat',
    //   'EMonitorApp/App/Report',
    //   'EMonitorApp/App/Report/PridatUpravit',
    //   'EMonitorApp/App/Report/Graf',
    //   'EMonitorApp/App/Report/Tabulka',
    //   'EMonitorApp/App/Report/SenzorZobrazenie',

    //   // 'EMonitorApp/Models/Project:Read',
    //   'EMonitorApp/Models/Zariadenie:Read',
    //   'EMonitorApp/Models/Report:Read',
    //   'EMonitorApp/Models/ReportSenzor:Read,Update',
    //   'EMonitorApp/Models/Senzor:Read,Update',
    //   'EMonitorApp/Models/SenzorKopia:Read,Update',
    //   'EMonitorApp/Models/SenzorHodnota:Read',
    //   'EMonitorApp/Models/SenzorTyp:Read',
    // ];

    // $permissions[\EMonitorApp\Models\User::ROLE_3] = [
    //   'EMonitorApp/App/Dashboard',
    //   'EMonitorApp/App/Lokalita',
    //   'EMonitorApp/App/Zariadenie',

    //   'EMonitorApp/Models/Senzor:Read',
    //   'EMonitorApp/Models/SenzorKopia:Read',
    //   // 'EMonitorApp/App/ImportDataloggerov',
    //   // 'EMonitorApp/App/Lokalita',
    //   // 'EMonitorApp/App/Zariadenie',
    //   // 'EMonitorApp/App/Zariadenie/PridatUpravit',
    //   // 'EMonitorApp/App/Zariadenie/Porovnat',
    //   // 'EMonitorApp/App/Report',
    //   // 'EMonitorApp/App/Report/PridatUpravit',
    //   // 'EMonitorApp/App/Report/Graf',
    //   // 'EMonitorApp/App/Report/Tabulka',

    //   // 'EMonitorApp/Models/Project:Read',
    //   // 'EMonitorApp/Models/ReportSenzor:Read',
    //   // 'EMonitorApp/Models/Senzor:Read',
    //   // 'EMonitorApp/Models/SenzorHodnota:Read',
    //   // 'EMonitorApp/Models/SenzorKopia:Read',
    //   // 'EMonitorApp/Models/Zariadenie:Read',
    //   // 'EMonitorApp/Models/Zariadenie/PridatUpravit:Read',
    //   // 'EMonitorApp/Models/Zariadenie/Porovnanie:Read',
    //   // 'EMonitorApp/Models/Report:Read',
    //   // 'EMonitorApp/Models/Report/PridatUpravit:Read',
    // ];

    return $permissions;
  }
}
