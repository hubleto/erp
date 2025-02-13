import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormCalendarSyncSource from "./FormCalendarSyncSource";

interface TableSourcesProps extends TableProps {
  // showHeader: boolean,
  // showFooter: boolean
}

interface TableSourcesState extends TableState {
}

export default class TableCalendarSyncSources extends Table<TableSourcesProps, TableSourcesState> {
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
    return <FormCalendarSyncSource {...formDescription}/>;
  }
}