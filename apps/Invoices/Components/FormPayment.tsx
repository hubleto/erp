import React, { Component } from 'react'
import HubletoForm, {HubletoFormProps, HubletoFormState} from "@hubleto/react-ui/ext/HubletoForm";

export interface FormPaymentProps extends HubletoFormProps {
}

interface FormPaymentState extends HubletoFormState {
}

export default class FormPayment extends HubletoForm<FormPaymentProps, FormPaymentState> {
  static defaultProps = {
    ...HubletoForm.defaultProps,
    icon: 'fas fa-file-invoice',
    description: {
      ui: { headerClassName: 'bg-indigo-50', },
    },
    renderWorkflowUi: true,
  }

  props: FormPaymentProps;
  state: FormPaymentState;

  translationContext: string = 'Hubleto\\App\\Community\\Invoices\\Loader';
  translationContextInner: string = 'Components\\FormPayment';

  constructor(props: FormPaymentProps) {
    super(props);
  }

  getStateFromProps(props: FormPaymentProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Invoice')}</b> },
        ...this.getCustomTabs()
      ],
    };
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
    }
  }

  getRecordFormUrl(): string {
    return 'invoices/paymentss/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    const R = this.state.record;
    return <>
      <small>{this.translate('Payment')}</small>
      <h2>{R.date}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          <div className="flex gap-2 mt-2">
            <div className='flex-1'>
              {this.inputWrapper('id_invoice')}
              {this.inputWrapper('date_payment')}
              {this.inputWrapper('amount')}
              {this.inputWrapper('is_advance_payment')}
            </div>
          </div>
        </>;
      break;
    }
  }
}
