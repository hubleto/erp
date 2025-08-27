import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import FormDashboard from './FormDashboard';

interface TableDashboardsProps extends TableProps {
}

interface TableDashboardsState extends TableState {
}

export default class TableDashboards extends Table<TableDashboardsProps, TableDashboardsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Dashboards/Models/Dashboard',
  }

  props: TableDashboardsProps;
  state: TableDashboardsState;

  translationContext: string = 'HubletoApp\\Community\\Dashboards\\Loader::Components\\TableDashboards';

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right';
    return params;
  }

  constructor(props: TableDashboardsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { idDashboard: this.state.recordId };
    return <FormDashboard {...formProps}/>;
  }
}