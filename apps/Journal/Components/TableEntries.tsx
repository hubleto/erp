import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface TableEntriesProps extends TableProps {
}

interface TableEntriesState extends TableState {
}

export default class TableEntries extends Table<TableEntriesProps, TableEntriesState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Journal/Models/Entry',
  }

  props: TableEntriesProps;
  state: TableEntriesState;

  translationContext: string = 'Hubleto\\App\\Community\\Journal\\Loader::Components\\TableEntries';

  constructor(props: TableEntriesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableEntriesProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  // renderForm(): JSX.Element {
  //   let formDescription = this.getFormProps();
    // return <CustomerFormActivity {...formDescription}/>;
  // }
}