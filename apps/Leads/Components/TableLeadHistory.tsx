import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface TableLeadHistoryProps extends TableProps {}
interface TableLeadHistoryState extends TableState {}

export default class TableLeadHistory extends Table<TableLeadHistoryProps, TableLeadHistoryState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Leads/Models/LeadHistory',
  }

  props: TableLeadHistoryProps;
  state: TableLeadHistoryState;

  translationContext: string = 'HubletoApp\\Community\\Leads\\Loader::Components\\TableLeadHistory';

  constructor(props: TableLeadHistoryProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableLeadHistoryProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }
}