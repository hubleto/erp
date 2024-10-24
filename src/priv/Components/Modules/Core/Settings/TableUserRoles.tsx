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
    model: 'CeremonyCrmApp/Modules/Core/Settings/Models/UserRole',
  }

  props: TableUserRolesProps;
  state: TableUserRolesState;

  constructor(props: TableUserRolesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  renderForm(): JSX.Element {
    let formDescription = this.getFormProps();
    return <FormUserRoles {...formDescription}/>;
  }
}