import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormTask from './FormTask';

interface TableTasksProps extends HubletoTableProps {
  externalModel?: string,
  externalId?: number,
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
      externalModel: this.props.externalModel,
      externalId: this.props.externalId,
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.customEndpointParams.externalModel = this.props.externalModel;
    formProps.customEndpointParams.externalId = this.props.externalId;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = {
      external_model: this.props.externalModel,
      external_id: this.props.externalId
    };
    return <FormTask {...formProps}/>;
  }
}