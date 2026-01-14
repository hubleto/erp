import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormPayment, { FormPaymentProps } from './FormPayment';

interface TablePaymentsProps extends TableExtendedProps {
  idInvoice?: number,
}

interface TablePaymentsState extends TableExtendedState {
}

export default class TablePayments extends TableExtended<TablePaymentsProps, TablePaymentsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
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
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/invoices/payments/' + (id > 0 ? id : 'add'));
  }

  renderForm(): JSX.Element {
    let formProps = this.getFormProps() as FormPaymentProps;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_invoice: this.props.idInvoice };
    return <FormPayment {...formProps}/>;
  }
}