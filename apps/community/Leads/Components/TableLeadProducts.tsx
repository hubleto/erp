import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';

interface TableLeadProductsProps extends TableProps {}

interface TableLeadProductsState extends TableState {}

export default class TableLeadProducts extends Table<TableLeadProductsProps, TableLeadProductsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Leads/Models/LeadProduct',
  }

  props: TableLeadProductsProps;
  state: TableLeadProductsState;

  translationContext: string = 'HubletoApp\\Community\\Leads\\Loader::Components\\TableLeadProducts';

  constructor(props: TableLeadProductsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }
}