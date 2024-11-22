import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';

interface TableCompanyDocumentsProps extends TableProps {}
interface TableCompanyDocumentsState extends TableState {}

export default class TableCompanyDocuments extends Table<TableCompanyDocumentsProps, TableCompanyDocumentsState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    formUseModalSimple: true,
    model: 'CeremonyCrmApp/Modules/Core/Customers/Models/CompanyDocument',
  }

  props: TableCompanyDocumentsProps;
  state: TableCompanyDocumentsState;

  constructor(props: TableCompanyDocumentsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableCompanyDocumentsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }
}