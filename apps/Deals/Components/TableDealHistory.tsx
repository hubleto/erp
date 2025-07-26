import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface TableDealHistoryProps extends TableProps {}
interface TableDealHistoryState extends TableState {}

export default class TableDealHistory extends Table<TableDealHistoryProps, TableDealHistoryState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Deals/Models/DealHistory',
  }

  props: TableDealHistoryProps;
  state: TableDealHistoryState;

  translationContext: string = 'HubletoApp\\Community\\Deals\\Loader::Components\\TableDealHistory';

  constructor(props: TableDealHistoryProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableDealHistoryProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }
}