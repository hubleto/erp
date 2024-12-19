import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormPipeline from './FormPipeline';

interface TablePipelinesProps extends TableProps {
}

interface TablePipelinesState extends TableState {
}

export default class TablePipelines extends Table<TablePipelinesProps, TablePipelinesState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    formUseModalSimple: true,
    model: 'CeremonyCrmMod/Settings/Models/Pipeline',
  }

  props: TablePipelinesProps;
  state: TablePipelinesState;

  translationContext: string = 'mod.core.settings.tablePipelines';

  constructor(props: TablePipelinesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  renderForm(): JSX.Element {
    let formDescription = this.getFormProps();
    return <FormPipeline {...formDescription}/>;
  }
}