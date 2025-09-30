import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormInventory from './FormInventory';

interface TableInventoryProps extends HubletoTableProps {
  idProduct?: number,
}

interface TableInventoryState extends HubletoTableState {
}

export default class TableInventory extends HubletoTable<TableInventoryProps, TableInventoryState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Warehouses/Models/Inventory',
  }

  props: TableInventoryProps;
  state: TableInventoryState;

  translationContext: string = 'Hubleto\\App\\Community\\Warehouses\\Loader';
  translationContextInner: string = 'Components\\TableInventory';

  constructor(props: TableInventoryProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableInventoryProps) {
    return {
      ...super.getStateFromProps(props),
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
      idProduct: this.props.idProduct,
    }
  }

  renderFooter(): JSX.Element {
    let totalQuantity = 0;

    for (let i in this.state.data?.data) {
      const row = this.state.data?.data[i];
      totalQuantity += parseFloat(row['quantity']);
    }

    return <>
      <div className="font-bold">
        {this.translate('Total quantity')}: {totalQuantity.toFixed(2)}<br/>
      </div>
    </>
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.customEndpointParams.idProduct = this.props.idProduct;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_product: this.props.idProduct };
    return <FormInventory {...formProps}/>;
  }
}