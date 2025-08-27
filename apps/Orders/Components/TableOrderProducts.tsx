import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';

interface TableOrderProductsProps extends HubletoTableProps {
  idOrder: number
}

interface TableOrderProductsState extends HubletoTableState {}

export default class TableOrderProducts extends HubletoTable<TableOrderProductsProps, TableOrderProductsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    model: 'Hubleto/App/Community/Orders/Models/OrderProduct',
  }

  props: TableOrderProductsProps;
  state: TableOrderProductsState;

  translationContext: string = 'Hubleto\\App\\Community\\Orders\\Loader::Components\\TableOrderProducts';

  constructor(props: TableOrderProductsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableOrderProductsProps) {
    return {
      ...super.getStateFromProps(props)
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right';
    return params;
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      idOrder: this.props.idOrder,
    }
  }
}