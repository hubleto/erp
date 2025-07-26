import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';

interface TableOrderProductsProps extends TableProps {
  sum?: string,
}

interface TableOrderProductsState extends TableState {}

export default class TableOrderProducts extends Table<TableOrderProductsProps, TableOrderProductsState> {
  static defaultProps = {
    ...Table.defaultProps,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Orders/Models/OrderProduct',
  }

  props: TableOrderProductsProps;
  state: TableOrderProductsState;

  translationContext: string = 'HubletoApp\\Community\\Orders\\Loader::Components\\TableOrderProducts';

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
    }
  }

  renderHeaderRight(): Array<JSX.Element> {
    let elements: Array<JSX.Element> = super.renderHeaderRight();

    return elements;
  }

  renderFooter(): JSX.Element {
    return <>
      <div className='flex flex-row justify-start md:justify-end'><strong className='mr-5'>{this.props.sum}</strong></div>
    </>;
  }
}