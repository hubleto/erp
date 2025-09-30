import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormTransaction from './FormTransaction';

interface TableTransactionItemsProps extends HubletoTableProps {
  idTransaction?: number,
  idProduct?: number,
}

interface TableTransactionItemsState extends HubletoTableState {
}

export default class TableTransaction extends HubletoTable<TableTransactionItemsProps, TableTransactionItemsState> {
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

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.customEndpointParams.idTransaction = this.props.idTransaction;
    formProps.customEndpointParams.idProduct = this.props.idProduct;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = {
      id_transaction: this.props.idTransaction,
      id_product: this.props.idProduct,
    };
    return <FormTransaction {...formProps}/>;
  }
}