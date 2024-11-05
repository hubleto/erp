import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';

interface TableLeadServicesProps extends TableProps {
}

interface TableLeadServicesState extends TableState {
}

export default class TableLeadServices extends Table<TableLeadServicesProps, TableLeadServicesState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    formUseModalSimple: true,
    model: 'CeremonyCrmApp/Modules/Sales/Sales/Models/LeadService',
  }

  props: TableLeadServicesProps;
  state: TableLeadServicesState;

  constructor(props: TableLeadServicesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }
}