import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/src/core/Components/HubletoTable';
import FormTask from './FormTask';

interface TableTasksProps extends HubletoTableProps {
  idProject?: number,
}

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
      idProject: this.props.idProject,
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.customEndpointParams.idProject = this.props.idProject;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_project: this.props.idProject };
    return <FormTask {...formProps}/>;
  }
}