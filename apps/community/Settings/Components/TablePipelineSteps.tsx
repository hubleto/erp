import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';

interface TablePipelineStepsProps extends TableProps {
}

interface TablePipelineStepsState extends TableState {
}

export default class TablePipelineSteps extends Table<TablePipelineStepsProps, TablePipelineStepsState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Settings/Models/PipelineStep',
  }

  props: TablePipelineStepsProps;
  state: TablePipelineStepsState;

  translationContext: string = 'mod.core.settings.tablePipelineSteps';

  constructor(props: TablePipelineStepsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }
}