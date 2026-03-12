import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import FormWorkflowStep from './FormWorkflowStep';

interface TableWorkflowStepsProps extends TableProps {
  idWorkflow: any,
}

interface TableWorkflowStepsState extends TableState {
  idWorkflow: any,
}

export default class TableWorkflowSteps extends Table<TableWorkflowStepsProps, TableWorkflowStepsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Workflow/Models/WorkflowStep',
  }

  props: TableWorkflowStepsProps;
  state: TableWorkflowStepsState;

  translationContext: string = 'Hubleto\\App\\Community\\Workflow\\Loader\\Loader';
  translationContextInner: string = 'Components\\TableWorkflowSteps';

  constructor(props: TableWorkflowStepsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableWorkflowStepsProps) {
    return {
      ...super.getStateFromProps(props),
      idWorkflow: props.idWorkflow,
    }
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      idWorkflow: this.state.idWorkflow,
    }
  }

  getFormModalProps() {
    return {
      ...super.getFormModalProps(),
      type: 'right wide',
    };
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.description = {
      defaultValues: {
        id_workflow: this.state.idWorkflow,
      },
      ui: { headerClassName: 'bg-indigo-50', },
    };
    return <FormWorkflowStep {...formProps}/>;
  }
}