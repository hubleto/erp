import React, { Component } from 'react'
import FormExtended, {FormExtendedProps, FormExtendedState} from "@hubleto/react-ui/ext/FormExtended";

export interface FormPaymentProps extends FormExtendedProps {
}

interface FormPaymentState extends FormExtendedState {
}

export default class FormPayment extends FormExtended<FormPaymentProps, FormPaymentState> {
  static defaultProps = {
    ...FormExtended.defaultProps,
    icon: 'fas fa-file-invoice',
    description: {
      ui: { headerClassName: 'bg-indigo-50', },
    },
    renderWorkflowUi: true,
  }

  props: FormPaymentProps;
  state: FormPaymentState;

  translationContext: string = 'Hubleto\\App\\Community\\Orders\\Loader';
  translationContextInner: string = 'Components\\FormPayment';

  constructor(props: FormPaymentProps) {
    super(props);
  }

  getStateFromProps(props: FormPaymentProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Order Payment')}</b> },
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
    return 'orderss/paymentss/' + (this.state.record.id > 0 ? this.state.record.id : 'add');
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
              {this.inputWrapper('id_order')}
              {this.inputWrapper('title')}
              {this.inputWrapper('date_due')}
              {this.inputWrapper('unit_price')}
              {this.inputWrapper('amount')}
              {this.inputWrapper('discount')}
              {this.inputWrapper('vat')}
            </div>
            <div className='flex-1'>
              {this.inputWrapper('notes')}
              <div className='bg-slate-50 p-2'>
                <b>{this.translate('Payment summary')}</b><br/>
                <table className='table-default dense w-full'>
                  <thead>
                    <tr>
                      <th></th>
                      <th>{this.translate('Without discount')}</th>
                      <th>{this.translate('With discount')}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><b>{R.title}</b></td>
                      <td className='text-nowrap'>
                        <div className='flex gap-2'>
                          <div>{globalThis.hubleto.currencyFormat(R.unit_price, 4)}</div>
                          <div>x</div>
                          <div>{globalThis.hubleto.numberFormat(R.amount, 4)}</div>
                        </div>
                      </td>
                      <td className='text-nowrap'>
                        <div className='flex gap-2'>
                          <div>{globalThis.hubleto.currencyFormat(R.unit_price * (1 - R.discount / 100), 4)}</div>
                          <div>x</div>
                          <div>{globalThis.hubleto.numberFormat(R.amount, 4)}</div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>{this.translate('Excluding VAT')}</td>
                      <td className='text-nowrap'>{globalThis.hubleto.currencyFormat(R.unit_price * R.amount, 4)}</td>
                      <td className='text-nowrap'>{globalThis.hubleto.currencyFormat(R.unit_price * R.amount * (1 - R.discount / 100), 4)}</td>
                    </tr>
                    <tr>
                      <td>Including {globalThis.hubleto.numberFormat(R.vat, 0)} % VAT</td>
                      <td className='text-nowrap'>{globalThis.hubleto.currencyFormat(R.unit_price * R.amount * (1 + R.vat / 100), 4)}</td>
                      <td className='text-nowrap'>{globalThis.hubleto.currencyFormat(R.unit_price * R.amount * (1 + R.vat / 100) * (1 - R.discount / 100), 4)}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              {this.inputWrapper('id_invoice_item')}
            </div>
          </div>
        </>;
      break;
    }
  }
}
