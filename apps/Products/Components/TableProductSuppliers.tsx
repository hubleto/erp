import React, { Component } from 'react'
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import FormProductSupplier from './FormProductSupplier';

interface TableProductSuppliersProps extends TableProps {
  idProduct: number,
}

interface TableProductSuppliersState extends TableState {}

export default class TableProductSuppliers extends Table<TableProductSuppliersProps, TableProductSuppliersState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'HubletoApp/Community/Products/Models/ProductSupplier',
  }

  props: TableProductSuppliersProps;
  state: TableProductSuppliersState;

  translationContext: string = 'HubletoApp\\Community\\Products\\Loader::Components\\TableProductSuppliers';

  constructor(props: TableProductSuppliersProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableProductSuppliersProps) {
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
    formProps.description = {
      defaultValues: { id_product: this.props.idProduct }
    };
    return <FormProductSupplier {...formProps}/>;
  }
}