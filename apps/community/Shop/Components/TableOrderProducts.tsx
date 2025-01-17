import React, { Component } from 'react'
import Table, { TableProps, TableState } from 'adios/Table';

interface TableOrderProductsProps extends TableProps {}

interface TableOrderProductsState extends TableState {}

export default class TableOrderProducts extends Table<TableOrderProductsProps, TableOrderProductsState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Shop/Models/OrderProduct',
  }

  props: TableOrderProductsProps;
  state: TableOrderProductsState;

  translationContext: string = 'mod.core.sales.TableOrderProducts';

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
}