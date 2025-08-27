import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface TableLeadDocumentsProps extends TableProps {}
interface TableLeadDocumentsState extends TableState {}

export default class TableLeadDocuments extends Table<TableLeadDocumentsProps, TableLeadDocumentsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Leads/Models/LeadDocument',
  }

  props: TableLeadDocumentsProps;
  state: TableLeadDocumentsState;

  translationContext: string = 'HubletoApp\\Community\\Leads\\Loader::Components\\TableLeadDocuments';

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