import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormInventory from './FormInventory';

interface TableInventoryProps extends TableExtendedProps {
  idProduct?: number,
}

interface TableInventoryState extends TableExtendedState {
}

export default class TableInventory extends TableExtended<TableInventoryProps, TableInventoryState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
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

  renderCell(columnName: string, column: any, data: any, options: any) {
    let cell = super.renderCell(columnName, column, data, options);

    if (columnName == 'quantity') {
      if (data[columnName] >= 0) {
        cell = <span className='text-green-800'>{data[columnName]}</span>;
      } else {
        cell = <span className='text-red-800'>{data[columnName]}</span>;
      }
    }

    return cell;

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