import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormMilestone from './FormMilestone';

interface TableMilestonesProps extends TableExtendedProps {
  idProject?: number,
}

interface TableMilestonesState extends TableExtendedState {
}

export default class TableMilestones extends TableExtended<TableMilestonesProps, TableMilestonesState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Projects/Models/Milestone',
  }

  props: TableMilestonesProps;
  state: TableMilestonesState;

  translationContext: string = 'Hubleto\\App\\Community\\Projects\\Loader';
  translationContextInner: string = 'Components\\TableMilestones';

  constructor(props: TableMilestonesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableMilestonesProps) {
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
    return <FormMilestone {...formProps}/>;
  }
}