import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';
import FormPayment, { FormPaymentProps } from './FormPayment';

interface TablePaymentsProps extends HubletoTableProps {
  idInvoice?: number,
}

interface TablePaymentsState extends HubletoTableState {
}

export default class TablePayments extends HubletoTable<TablePaymentsProps, TablePaymentsState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Invoices/Models/Payment',
  }

  props: TablePaymentsProps;
  state: TablePaymentsState;

  translationContext: string = 'Hubleto\\App\\Community\\Invoices\\Loader';
  translationContextInner: string = 'Components\\TablePayments';

  constructor(props: TablePaymentsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TablePaymentsProps) {
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
      idInvoice: this.props.idInvoice,
    }
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.main.config.projectUrl + '/invoices/payments/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormPaymentProps;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_invoice: this.props.idInvoice };
    return <FormPayment {...formProps}/>;
  }
}