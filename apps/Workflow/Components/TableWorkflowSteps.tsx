import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface TableWorkflowStepsProps extends TableProps {
}

interface TableWorkflowStepsState extends TableState {
}

export default class TableWorkflowSteps extends Table<TableWorkflowStepsProps, TableWorkflowStepsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Workflow/Models/WorkflowStep',
  }

  props: TableWorkflowStepsProps;
  state: TableWorkflowStepsState;

  translationContext: string = 'Hubleto\\App\\Community\\Workflow\\Loader::Components\\TableWorkflowSteps';

  constructor(props: TableWorkflowStepsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getFormModalProps() {
    return {
      ...super.getFormModalProps(),
      type: 'right wide',
    };
  }
}