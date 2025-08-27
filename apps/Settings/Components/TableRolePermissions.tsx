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
    model: 'Hubleto/App/Community/Settings/Models/RolePermission',
  }

  props: TableRolePermissionsProps;
  state: TableRolePermissionsState;

  translationContext: string = 'Hubleto\\App\\Community\\Settings\\Loader::Components\\TableRolePermissions';

  constructor(props: TableRolePermissionsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }
}