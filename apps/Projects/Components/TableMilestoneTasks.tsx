import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormMilestoneTask from './FormMilestoneTask';

interface TableMilestoneTasksProps extends TableExtendedProps {
  idMilestone?: number,
}

interface TableMilestoneTasksState extends TableExtendedState {
}

export default class TableMilestoneTasks extends TableExtended<TableMilestoneTasksProps, TableMilestoneTasksState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Projects/Models/MilestoneTask',
  }

  props: TableMilestoneTasksProps;
  state: TableMilestoneTasksState;

  translationContext: string = 'Hubleto\\App\\Community\\Projects\\Loader';
  translationContextInner: string = 'Components\\TableMilestoneTasks';

  constructor(props: TableMilestoneTasksProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableMilestoneTasksProps) {
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
      idMilestone: this.props.idMilestone,
    }
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.customEndpointParams.idMilestone = this.props.idMilestone;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_milestone: this.props.idMilestone };
    return <FormMilestoneTask {...formProps}/>;
  }
}