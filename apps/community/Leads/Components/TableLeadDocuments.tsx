import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';

interface TableLeadDocumentsProps extends TableProps {}
interface TableLeadDocumentsState extends TableState {}

export default class TableLeadDocuments extends Table<TableLeadDocumentsProps, TableLeadDocumentsState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Leads/Models/LeadDocument',
  }

  props: TableLeadDocumentsProps;
  state: TableLeadDocumentsState;

  translationContext: string = 'HubletoApp/Community/Leads/Components/TableLeadDocuments';

  constructor(props: TableLeadDocumentsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableLeadDocumentsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }
}