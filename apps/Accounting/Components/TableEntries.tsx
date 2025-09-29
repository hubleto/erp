import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import FormEntry from "@hubleto/apps/Accounting/Components/FormEntry";

interface TableEntriesProps extends TableProps {
}

interface TableEntriesState extends TableState {
}

export default class TableEntries extends Table<TableEntriesProps, TableEntriesState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Accounting/Models/Entry',
  }

  props: TableEntriesProps;
  state: TableEntriesState;

  translationContext: string = 'Hubleto\\App\\Community\\Accounting\\Loader::Components\\TableEntries';

  constructor(props: TableEntriesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableEntriesProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  renderForm(): JSX.Element {
    let formDescription = this.getFormProps();
    return <FormEntry {...formDescription}/>;
  }
}