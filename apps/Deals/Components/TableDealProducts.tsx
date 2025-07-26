import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface TableDealProductsProps extends TableProps {}

interface TableDealProductsState extends TableState {}

export default class TableDealProducts extends Table<TableDealProductsProps, TableDealProductsState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Deals/Models/DealProduct',
  }

  props: TableDealProductsProps;
  state: TableDealProductsState;

  translationContext: string = 'HubletoApp\\Community\\Deals\\Loader::Components\\TableDealProducts';

  constructor(props: TableDealProductsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }
}