import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import FormPipeline from './FormPipeline';

interface TablePipelinesProps extends TableProps {
}

interface TablePipelinesState extends TableState {
}

export default class TablePipelines extends Table<TablePipelinesProps, TablePipelinesState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Pipeline/Models/Pipeline',
  }

  props: TablePipelinesProps;
  state: TablePipelinesState;

  translationContext: string = 'HubletoApp\\Community\\Pipeline\\Loader::Components\\TablePipelines';

  constructor(props: TablePipelinesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  renderForm(): JSX.Element {
    let formDescription = this.getFormProps();
    return <FormPipeline {...formDescription}/>;
  }
}