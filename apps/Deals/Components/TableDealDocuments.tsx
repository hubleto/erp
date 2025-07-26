import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface TableDealDocumentsProps extends TableProps {}
interface TableDealDocumentsState extends TableState {}

export default class TableDealDocuments extends Table<TableDealDocumentsProps, TableDealDocumentsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Deals/Models/DealDocument',
  }

  props: TableDealDocumentsProps;
  state: TableDealDocumentsState;

  translationContext: string = 'HubletoApp\\Community\\Deals\\Loader::Components\\TableDealDocuments';

  constructor(props: TableDealDocumentsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableDealDocumentsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }
}