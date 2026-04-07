import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import FormAutomat from './FormAutomat';

interface TableAutomatsProps extends TableProps {
}

interface TableAutomatsState extends TableState {
}

export default class TableAutomats extends Table<TableAutomatsProps, TableAutomatsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Workflow/Models/Automat',
  }

  props: TableAutomatsProps;
  state: TableAutomatsState;

  translationContext: string = 'Hubleto\\App\\Community\\Workflow\\Loader';
  translationContextInner: string = 'Components\\TableAutomats';

  constructor(props: TableAutomatsProps) {
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
    return <FormAutomat {...formDescription}/>;
  }
}