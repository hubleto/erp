import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormUserRoles from './FormUserRoles';

interface TableUserRolesProps extends TableProps {
}

interface TableUserRolesState extends TableState {
}

export default class TableUserRoles extends Table<TableUserRolesProps, TableUserRolesState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Settings/Models/UserRole',
  }

  props: TableUserRolesProps;
  state: TableUserRolesState;

  translationContext: string = 'HubletoApp/Community/Settings/Components/TableUserRoles';

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
    return <FormUserRoles {...formDescription}/>;
  }
}