import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';

interface TableItemsProps extends HubletoTableProps {
  idOrder: number
}

interface TableItemsState extends HubletoTableState {}

export default class TableItems extends HubletoTable<TableItemsProps, TableItemsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    model: 'Hubleto/App/Community/Orders/Models/Item',
  }

  props: TableItemsProps;
  state: TableItemsState;

  translationContext: string = 'Hubleto\\App\\Community\\Orders\\Loader';
  translationContextInner: string = 'Components\\TableItems';

  constructor(props: TableItemsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableItemsProps) {
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