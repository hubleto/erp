import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';
import FormDeal from './FormDeal';

interface TableDealsProps extends TableProps {
}

interface TableDealsState extends TableState {
}

export default class TableDeals extends Table<TableDealsProps, TableDealsState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 20,
    formUseModalSimple: true,
    model: 'CeremonyCrmApp/Modules/Sales/Sales/Models/Deal',
  }

  props: TableDealsProps;
  state: TableDealsState;

  constructor(props: TableDealsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableDealsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  renderForm(): JSX.Element {
    let formDescription = this.getFormProps();
    return <FormDeal {...formDescription}/>;
  }
}