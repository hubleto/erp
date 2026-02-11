import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormMilestoneReport from './FormMilestoneReport';

interface TableMilestoneReportsProps extends TableExtendedProps {
  idMilestone?: number,
}

interface TableMilestoneReportsState extends TableExtendedState {
}

export default class TableMilestoneReports extends TableExtended<TableMilestoneReportsProps, TableMilestoneReportsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Projects/Models/MilestoneReport',
  }

  props: TableMilestoneReportsProps;
  state: TableMilestoneReportsState;

  translationContext: string = 'Hubleto\\App\\Community\\Projects\\Loader';
  translationContextInner: string = 'Components\\TableMilestoneReports';

  constructor(props: TableMilestoneReportsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableMilestoneReportsProps) {
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
    return <FormMilestoneReport {...formProps}/>;
  }
}