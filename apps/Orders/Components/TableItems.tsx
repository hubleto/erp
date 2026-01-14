import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';

interface TableItemsProps extends TableExtendedProps {
  idOrder: number
}

interface TableItemsState extends TableExtendedState {}

export default class TableItems extends TableExtended<TableItemsProps, TableItemsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
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