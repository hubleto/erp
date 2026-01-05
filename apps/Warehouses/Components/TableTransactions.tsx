import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormTransaction from './FormTransaction';

interface TableTransactionsProps extends HubletoTableProps {
  idProduct?: number,
  direction?: number,
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
  translationContextInner: string = 'Components\\TableTransactions';

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
      direction: this.props.direction,
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/warehouses/transactions/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps();

    formProps.direction = this.props.direction;

    formProps.customEndpointParams.idProduct = this.props.idProduct;
    formProps.customEndpointParams.direction = this.props.direction;
    
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = {
      id_product: this.props.idProduct,
      direction: this.props.direction,
    };

    return <FormTransaction {...formProps}/>;
  }
}