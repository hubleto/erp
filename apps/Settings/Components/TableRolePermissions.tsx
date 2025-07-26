import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface TableRolePermissionsProps extends TableProps {
}

interface TableRolePermissionsState extends TableState {
}

export default class TableRolePermissions extends Table<TableRolePermissionsProps, TableRolePermissionsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Settings/Models/RolePermission',
  }

  props: TableRolePermissionsProps;
  state: TableRolePermissionsState;

  translationContext: string = 'HubletoApp\\Community\\Settings\\Loader::Components\\TableRolePermissions';

  constructor(props: TableRolePermissionsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }
}