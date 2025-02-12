import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormSource from "./FormSource";

interface TableSourcesProps extends TableProps {
  // showHeader: boolean,
  // showFooter: boolean
}

interface TableSourcesState extends TableState {
}

export default class TableSources extends Table<TableSourcesProps, TableSourcesState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/CalendarSync/Models/Source',
  }

  props: TableSourcesProps;
  state: TableSourcesState;

  translationContext: string = 'hubleto.app.calendarsync.tableSources';

  constructor(props: TableSourcesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  renderForm(): JSX.Element {
    let formDescription = this.getFormProps();
    return <FormSource {...formDescription}/>;
  }
}