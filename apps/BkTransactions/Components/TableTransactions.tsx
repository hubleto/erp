import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import FormTransaction from "@hubleto/apps/BkTransactions/Components/FormTransaction";

interface TableTransactionsProps extends TableProps {
}

interface TableTransactionsState extends TableState {
}

export default class TableTransactions extends Table<TableTransactionsProps, TableTransactionsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/BkTransactions/Models/Transaction',
  }

  props: TableTransactionsProps;
  state: TableTransactionsState;

  translationContext: string = 'Hubleto\\App\\Community\\BkTransactions\\Loader::Components\\TableTransactions';

  constructor(props: TableTransactionsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableTransactionsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  renderForm(): JSX.Element {
    let formDescription = this.getFormProps();
    return <FormTransaction {...formDescription}/>;
  }
}