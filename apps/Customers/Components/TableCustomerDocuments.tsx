import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface TableCustomerDocumentsProps extends TableProps {}
interface TableCustomerDocumentsState extends TableState {}

export default class TableCustomerDocuments extends Table<TableCustomerDocumentsProps, TableCustomerDocumentsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Customers/Models/CustomerDocument',
  }

  props: TableCustomerDocumentsProps;
  state: TableCustomerDocumentsState;

  translationContext: string = 'HubletoApp\\Community\\Customers\\Loader::Components\\TableCustomerDocuments';

  constructor(props: TableCustomerDocumentsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableCustomerDocumentsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }
}