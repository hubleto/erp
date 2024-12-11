import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';

interface TableDealServicesProps extends TableProps {
  dealTotal?: any
}

interface TableDealServicesState extends TableState {
}

export default class TableDealServices extends Table<TableDealServicesProps, TableDealServicesState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    formUseModalSimple: true,
    model: 'CeremonyCrmMod/Sales/Deals/Models/DealService',
  }

  props: TableDealServicesProps;
  state: TableDealServicesState;

  translationContext: string = 'mod.core.sales.tableDealServices';

  constructor(props: TableDealServicesProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }


  renderFooter(): JSX.Element {
    return <>
      <div className='flex flexx-row justify-end'><strong className='mr-4'>{this.props.dealTotal}</strong></div>
    </>;
  }
}