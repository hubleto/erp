import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';

interface TableRolePermissionsProps extends TableProps {
}

interface TableRolePermissionsState extends TableState {
}

export default class TableRolePermissions extends Table<TableRolePermissionsProps, TableRolePermissionsState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    formUseModalSimple: true,
    model: 'HubletoApp/Settings/Models/RolePermission',
  }

  props: TableRolePermissionsProps;
  state: TableRolePermissionsState;

  translationContext: string = 'mod.core.settings.tableRolePermissions';

  constructor(props: TableRolePermissionsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }
}