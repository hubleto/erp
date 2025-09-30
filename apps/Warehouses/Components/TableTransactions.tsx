import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormTransaction from './FormTransaction';

interface TableTransactionsProps extends HubletoTableProps {
  idProduct?: number,
}

interface TableTransactionsState extends HubletoTableState {
}

export default class TableTransaction extends HubletoTable<TableTransactionsProps, TableTransactionsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Warehouses/Models/Transaction',
  }

  props: TableTransactionsProps;
  state: TableTransactionsState;

  translationContext: string = 'Hubleto\\App\\Community\\Warehouses\\Loader';
  translationContextInner: string = 'Components\\TableTransaction';

  constructor(props: TableTransactionsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableTransactionsProps) {
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

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();
    formProps.customEndpointParams.idProduct = this.props.idProduct;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_product: this.props.idProduct };
    return <FormTransaction {...formProps}/>;
  }
}