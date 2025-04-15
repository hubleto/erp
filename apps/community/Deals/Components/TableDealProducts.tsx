import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';

interface TableDealProductsProps extends TableProps {
  dealTotal?: any
}

interface TableDealProductsState extends TableState {
}

export default class TableDealProducts extends Table<TableDealProductsProps, TableDealProductsState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
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


  renderFooter(): JSX.Element {
    return <>
      <div className='flex flex-row justify-start md:justify-end'><strong className='mr-4'>{this.props.dealTotal}</strong></div>
    </>;
  }
}