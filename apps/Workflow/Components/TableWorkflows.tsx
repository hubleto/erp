import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import FormWorkflow from './FormWorkflow';

interface TableWorkflowsProps extends TableProps {
}

interface TableWorkflowsState extends TableState {
}

export default class TableWorkflows extends Table<TableWorkflowsProps, TableWorkflowsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Workflow/Models/Workflow',
  }

  props: TableWorkflowsProps;
  state: TableWorkflowsState;

  translationContext: string = 'Hubleto\\App\\Community\\Workflow\\Loader\\Loader';
  translationContextInner: string = 'Components\\TableWorkflows';

  constructor(props: TableWorkflowsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getFormModalProps() {
    return {
      ...super.getFormModalProps(),
      type: 'right wide'
    }
  }

  renderForm(): JSX.Element {
    let formDescription = this.getFormProps();
    return <FormWorkflow {...formDescription}/>;
  }
}