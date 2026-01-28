import React, { Component } from 'react'
import FormExtended, {FormExtendedProps, FormExtendedState} from "@hubleto/react-ui/ext/FormExtended";

export interface FormProfileProps extends FormExtendedProps {
}

interface FormProfileState extends FormExtendedState {
}

export default class FormProfile extends FormExtended<FormProfileProps, FormProfileState> {
  static defaultProps = {
    ...FormExtended.defaultProps,
    icon: 'fas fa-file-invoice',
    description: {
      ui: { headerClassName: 'bg-indigo-50', },
    },
  }

  props: FormProfileProps;
  state: FormProfileState;

  translationContext: string = 'Hubleto\\App\\Community\\Invoices\\Loader';
  translationContextInner: string = 'Components\\FormProfile';

  constructor(props: FormProfileProps) {
    super(props);
  }

  getStateFromProps(props: FormProfileProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Profile')}</b> },
        { uid: 'invoice-rendering', title: <b>{this.translate('Invoice rendering')}</b> },
        { uid: 'e-mails', title: <b>{this.translate('E-mails')}</b> },
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
    return 'invoices/profiles/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
  }

  renderTitle(): JSX.Element {
    const R = this.state.record;
    return <>
      <small>{this.translate('Invoice profile')}</small>
      <h2>{R.name}</h2>
    </>;
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          <div className="flex gap-2 mt-2">
            <div className='flex-1'>
              {this.inputWrapper('is_default')}
              {this.inputWrapper('name')}
              {this.inputWrapper('headline')}
              {this.inputWrapper('id_company')}
              {this.inputWrapper('id_currency')}
              {this.inputWrapper('id_payment_method')}
              {this.inputWrapper('iban')}
              {this.inputWrapper('swift')}
              {this.inputWrapper('due_days')}
              {this.inputWrapper('numbering_pattern')}
              {this.inputWrapper('invoice_type_prefixes')}
            </div>
          </div>
        </>;
      break;
      case 'invoice-rendering':
        return <>
          <div className="flex gap-2 mt-2">
            <div className='flex-1'>
              {this.inputWrapper('id_template')}
              {this.inputWrapper('stamp_and_signature')}
            </div>
          </div>
        </>;
      break;
      case 'e-mails':
        return <>
          {this.inputWrapper('id_sender_account')}
          <div className='card mt-2'>
            <div className='card-header'>
              {this.translate('Send invoice')}
            </div>
            <div className='card-body'>
              {this.inputWrapper('mail_send_invoice_subject')}
              {this.inputWrapper('mail_send_invoice_body')}
              {this.inputWrapper('mail_send_invoice_cc')}
              {this.inputWrapper('mail_send_invoice_bcc')}
            </div>
          </div>
          <div className='card mt-2'>
            <div className='card-header'>
              {this.translate('Send warning about due invoice')}
            </div>
            <div className='card-body'>
              {this.inputWrapper('mail_send_due_warning_subject')}
              {this.inputWrapper('mail_send_due_warning_body')}
              {this.inputWrapper('mail_send_due_warning_cc')}
              {this.inputWrapper('mail_send_due_warning_bcc')}
            </div>
          </div>
        </>;
      break;
    }
  }
}
