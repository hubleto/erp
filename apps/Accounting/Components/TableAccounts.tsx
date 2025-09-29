import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import FormAccount from './FormAccount';

interface TableAccountsProps extends TableProps {
}

interface TableAccountsState extends TableState {
}

export default class TableAccounts extends Table<TableAccountsProps, TableAccountsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
  }

  props: TableAccountsProps;
  state: TableAccountsState;

  translationContext: string = 'Hubleto\\App\\Community\\Accounting\\Loader::Components\\TableAccounts';

  constructor(props: TableAccountsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableAccountsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  renderForm(): JSX.Element {
    let formDescription = this.getFormProps();
    return <FormAccount {...formDescription}/>;
  }
}