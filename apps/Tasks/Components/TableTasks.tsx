import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormTask from './FormTask';

interface TableTasksProps extends HubletoTableProps { }

interface TableTasksState extends HubletoTableState {
}

export default class TableTasks extends HubletoTable<TableTasksProps, TableTasksState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Tasks/Models/Task',
  }

  props: TableTasksProps;
  state: TableTasksState;

  translationContext: string = 'HubletoApp\\Community\\Tasks::Components\\TableTasks';

  constructor(props: TableTasksProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableTasksProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wide';
    return params;
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    return <FormTask {...formProps}/>;
  }
}