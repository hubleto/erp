import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';

interface TableContactsProps extends TableProps {
  // showHeader: boolean,
  // showFooter: boolean
}

interface TableContactsState extends TableState {
}

export default class TableContacts extends Table<TableContactsProps, TableContactsState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    formUseModalSimple: true,
    model: 'CeremonyCrmMod/Customers/Models/Contact',
  }

  props: TableContactsProps;
  state: TableContactsState;

  translationContext: string = 'ceremonycrmmod.core.customers.tableContacts';

  constructor(props: TableContactsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }
}