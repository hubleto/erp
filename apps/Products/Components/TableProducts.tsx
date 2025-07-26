import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import FormProduct from './FormProduct';

interface TableProductsProps extends TableProps {}

interface TableProductsState extends TableState {}

export default class TableProducts extends Table<TableProductsProps, TableProductsState> {

  static defaultProps = {
    ...Table.defaultProps,
    orderBy: {
      field: "id",
      direction: "asc"
    },
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Products/Models/Product',
  }

  props: TableProductsProps;
  state: TableProductsState;

  translationContext: string = 'HubletoApp\\Community\\Products\\Loader::Components\\TableProducts';

  constructor(props: TableProductsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableProductsProps) {
    return {
      ...super.getStateFromProps(props)
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wide';
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

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    return <FormProduct {...formProps}/>;
  }
}