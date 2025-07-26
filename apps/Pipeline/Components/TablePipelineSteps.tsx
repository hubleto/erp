import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface TablePipelineStepsProps extends TableProps {
}

interface TablePipelineStepsState extends TableState {
}

export default class TablePipelineSteps extends Table<TablePipelineStepsProps, TablePipelineStepsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Pipeline/Models/PipelineStep',
  }

  props: TablePipelineStepsProps;
  state: TablePipelineStepsState;

  translationContext: string = 'HubletoApp\\Community\\Pipeline\\Loader::Components\\TablePipelineSteps';

  constructor(props: TablePipelineStepsProps) {
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