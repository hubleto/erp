import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormProduct from './FormProduct';

interface TableProductsProps extends HubletoTableProps {}

interface TableProductsState extends HubletoTableState {}

export default class TableProducts extends HubletoTable<TableProductsProps, TableProductsState> {

  static defaultProps = {
    ...HubletoTable.defaultProps,
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