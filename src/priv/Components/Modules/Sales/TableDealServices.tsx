import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';

interface TableDealServicesProps extends TableProps {
}

interface TableDealServicesState extends TableState {
}

export default class TableDealServices extends Table<TableDealServicesProps, TableDealServicesState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    formUseModalSimple: true,
    model: 'CeremonyCrmApp/Modules/Sales/Sales/Models/DealService',
  }

  props: TableDealServicesProps;
  state: TableDealServicesState;

  constructor(props: TableDealServicesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }
}