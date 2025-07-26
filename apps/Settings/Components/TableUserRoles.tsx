import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import FormUserRole from './FormUserRole';

interface TableUserRolesProps extends TableProps {
}

interface TableUserRolesState extends TableState {
}

export default class TableUserRoles extends Table<TableUserRolesProps, TableUserRolesState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Settings/Models/UserRole',
  }

  props: TableUserRolesProps;
  state: TableUserRolesState;

  translationContext: string = 'HubletoApp\\Community\\Settings\\Loader::Components\\TableUserRoles';

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right';
    return params;
  }

  constructor(props: TableUserRolesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  renderForm(): JSX.Element {
    let formDescription = this.getFormProps();
    return <FormUserRole {...formDescription}/>;
  }
}