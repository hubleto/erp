import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormTransactionItem from './FormTransactionItem';

interface TableTransactionItemsProps extends HubletoTableProps {
  idTransaction?: number,
  idProduct?: number,
}

interface TableTransactionItemsState extends HubletoTableState {
}

export default class TableTransactionItems extends HubletoTable<TableTransactionItemsProps, TableTransactionItemsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Warehouses/Models/TransactionItem',
  }

  props: TableTransactionItemsProps;
  state: TableTransactionItemsState;

  translationContext: string = 'Hubleto\\App\\Community\\Warehouses\\Loader';
  translationContextInner: string = 'Components\\TableTransactionItems';

  constructor(props: TableTransactionItemsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableTransactionItemsProps) {
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
      idTransaction: this.props.idTransaction,
      idProduct: this.props.idProduct,
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/warehouses/transactions/items/' + (id > 0 ? id : 'add'));
  }

  cellClassName(columnName: string, column: any, rowData: any) {
    let cellClassName = super.cellClassName(columnName, column, rowData);

    if (columnName == 'id_transaction') {
      if (rowData.TRANSACTION.id_location_old > 0 && rowData.TRANSACTION.id_location_new > 0) {
        cellClassName += ' bg-blue-50';
      } else if (rowData.TRANSACTION.id_location_old > 0) {
        cellClassName += ' bg-red-50';
      } else if (rowData.TRANSACTION.id_location_new > 0) {
        cellClassName += ' bg-green-50';
      }
    }

    return cellClassName;
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    let cell = super.renderCell(columnName, column, data, options);

    if (columnName == 'id_transaction') {
      if (data.TRANSACTION.id_location_old > 0 && data.TRANSACTION.id_location_new > 0) {
        cell = <><i className='fas fa-refresh'></i> {cell}</>
      } else if (data.TRANSACTION.id_location_old > 0) {
        cell = <><i className='fas fa-minus'></i> {cell}</>
      } else if (data.TRANSACTION.id_location_new > 0) {
        cell = <><i className='fas fa-plus'></i> {cell}</>
      }
    }

    // if (columnName == 'quantity') {
    //   if (data.TRANSACTION.direction == 1) {
    //     cell = <span className='text-green-800'>{data[columnName]}</span>;
    //   } else {
    //     cell = <span className='text-red-800'>- {data[columnName]}</span>;
    //   }
    // }

    return cell;

  }

  renderFooter(): JSX.Element {
    let totalQuantity = 0;
    for (let i in this.state.data?.data) {
      const row = this.state.data?.data[i];

      if (row.TRANSACTION.direction == 1) // inbound
        totalQuantity += parseFloat(row['quantity']);
      else // outbound
        totalQuantity -= parseFloat(row['quantity']);
    }

    return <>
      <div className="font-bold">
        {this.translate('Total quantity')}: {totalQuantity.toFixed(2)} €<br/>
      </div>
    </>
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.customEndpointParams.idTransaction = this.props.idTransaction;
    formProps.customEndpointParams.idProduct = this.props.idProduct;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = {
      id_transaction: this.props.idTransaction,
      id_product: this.props.idProduct,
    };
    return <FormTransactionItem {...formProps}/>;
  }
}